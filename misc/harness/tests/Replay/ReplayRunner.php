<?php

use ALT\Core\Notifications;
use ALT\Core\Globals;
use ALT\Managers\Cards;
use ALT\Managers\Meeples;
use ALT\Managers\Players;

/**
 * Replays a BGA game in the local engine.
 *
 * The replay holds outputs (notifications), not inputs, so each request is rebuilt by search:
 * every action the current state allows is tried inside a SQL transaction, and the one whose
 * notifications equal the recorded ones is committed. If none does, the game diverges there.
 * Randomness (storm layout, deck shuffles) is forced to what the replay revealed.
 */
class ReplayRunner
{
  public altered $game;
  /** @var array<int, array{move: int, action: string}> */
  public array $played = [];
  public ?array $divergence = null;
  /** @var string[] */
  public array $log = [];
  /** @var string[] every candidate tried at the last request, with why it did not match */
  public array $attempts = [];

  public function __construct(private Replay $replay, private array $options = [])
  {
  }

  public function run(?int $maxMoves = null): bool
  {
    Table::$environment = 'prod';
    $this->forceRandomness();
    $this->game = altered::harnessCreate($this->replay->names(), $this->options);
    Globals::setTokenStyles($this->replay->tokenStyles());
    foreach ($this->replay->preferences() as $pId => $prefs) {
      foreach ($prefs as $prefId => $value) {
        BgaDb::query("INSERT INTO user_preferences (player_id, pref_id, pref_value) VALUES ($pId, $prefId, $value)");
      }
    }
    $started = false;
    foreach ($this->replay->moves as $move => $expected) {
      // Table creation, done above, spans the first requests that carry no game notification
      $started = $started || !empty($expected);
      if ($started) {
        $this->moveList[] = [$move, $expected];
      }
    }
    foreach (array_keys($this->moveList) as $index) {
      if ($maxMoves !== null && $index >= $maxMoves) {
        break;
      }
      if (!$this->playMove($index)) {
        return false;
      }
    }
    return true;
  }

  /** Number of requests to replay (table creation excluded) */
  public function requestCount(): int
  {
    return count($this->moveList);
  }

  /** @var array<int, array{0: int, 1: array}> requests to replay: [move id, expected notifications] */
  private array $moveList = [];

  /** Requests replayed ahead to tell apart actions that a request alone cannot */
  private const LOOKAHEAD = 3;

  private function forceRandomness(): void
  {
    $storm = $this->replay->storm();
    $stormConsumed = false;
    $rotations = array_map(fn($s) => $s[1] ? 1 : 0, array_values($storm));
    $deckOrders = [];
    foreach ($this->replay->players as $pId) {
      $deckOrders[$pId] = $this->replay->revealOrder($pId);
    }

    \Harness\Rng::$shuffle = function (array $a) use ($storm, &$stormConsumed, $deckOrders) {
      $sorted = $a;
      sort($sorted);
      if (!$stormConsumed && $sorted == [1, 2, 3] && count($storm) == 3) {
        $stormConsumed = true;
        return array_map(fn($s) => $s[0], array_values($storm));
      }
      // Deck: pieces get state = index and the top of the deck is the highest state,
      // so the first card drawn must be last. Cards never revealed go to the bottom.
      foreach ($deckOrders as $order) {
        $known = array_values(array_filter($order, fn($id) => in_array($id, $a)));
        if (!empty($known)) {
          $unknown = array_values(array_filter($a, fn($id) => !in_array((int) $id, $known)));
          return array_merge($unknown, array_reverse($known));
        }
      }
      return null;
    };
    \Harness\Rng::$randInt = function ($min, $max) use (&$rotations) {
      if ($min == 0 && $max == 1 && !empty($rotations)) {
        return array_shift($rotations);
      }
      // Dice of the current request, as shown by its "roll" notifications
      return ($min == 1 && $max == 6 && !empty($this->dice)) ? array_shift($this->dice) : null;
    };
  }

  /** @var int[] die values the current request is expected to roll */
  private array $dice = [];

  private function prepareRandomness(int $move, array $expected): void
  {
    $this->dice = [];
    foreach ($expected['table'] ?? [] as $n) {
      if ($n['type'] == 'roll') {
        foreach ((array) ($n['args']['rolls'] ?? []) as $roll) {
          $this->dice[] = (int) $roll;
        }
      }
    }
    // Cards can go back into a deck (and be shuffled in), so the order forced at the initial
    // shuffle is not enough: before each request, put on top of each deck the cards that the
    // replay shows leaving it next, in that order. Within this request the order is only known per
    // channel, so each interleaving of the channels is a variant to try.
    $perPlayer = [];
    foreach ($this->replay->players as $pId) {
      $rows = BgaDb::conn()->query("SELECT card_id FROM cards WHERE card_location = 'deck-$pId' ORDER BY card_state")->fetch_all();
      $deck = array_map('intval', array_column($rows, 0));
      if (empty($deck)) {
        continue;
      }
      $inDeck = fn($ids) => array_values(array_filter($ids, fn($id) => in_array($id, $deck)));
      $lists = array_values(array_filter(array_map($inDeck, $this->replay->revealedIn($pId, $move))));
      $now = array_merge(...array_merge([[]], $lists));
      $later = array_values(array_diff($inDeck($this->replay->revealOrder($pId, $move + 1)), $now));
      $rest = array_values(array_diff($deck, $now, $later));
      $perPlayer[$pId] = array_map(fn($order) => array_merge($order, $later, ['rest' => $rest]), self::interleavings($lists));
    }
    $this->deckVariants = [[]];
    foreach ($perPlayer as $pId => $orders) {
      $next = [];
      foreach ($this->deckVariants as $variant) {
        foreach ($orders as $order) {
          $next[] = $variant + [$pId => $order];
        }
      }
      $this->deckVariants = array_slice($next, 0, 24);
    }
    $this->applyDeckOrder($this->deckVariants[0]);

    // After an undo, catch up with ids consumed on BGA by the rows it removed: the next new id
    // the replay shows tells where the counter was
    $this->firstNewIds ??= $this->replay->firstNewIds();
    foreach ($this->afterUndo ? ($this->firstNewIds[$move] ?? []) : [] as $table => $id) {
      unset($this->afterUndo[$table]);
      $next = (int) BgaDb::conn()->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table'")->fetch_row()[0];
      $maxId = (int) BgaDb::conn()->query('SELECT COALESCE(MAX(' . ($table == 'cards' ? 'card_id' : 'meeple_id') . "), 0) FROM `$table`")->fetch_row()[0];
      if ($id > $next && $id > $maxId) {
        BgaDb::query("ALTER TABLE `$table` AUTO_INCREMENT = $id");
      }
    }
  }

  private ?array $firstNewIds = null;
  /** @var array<int, array<int, array>> deck orders to try for the current request: [player id => [ids top first..., 'rest' => ids]] */
  private array $deckVariants = [[]];

  private function applyDeckOrder(array $variant): void
  {
    foreach ($variant as $order) {
      $rest = $order['rest'];
      unset($order['rest']);
      // Top of the deck is the highest state
      foreach (array_merge($rest, array_reverse(array_values($order))) as $state => $id) {
        BgaDb::query("UPDATE cards SET card_state = $state WHERE card_id = $id");
      }
    }
    Cards::invalidate();
  }

  /** Every merge of the lists that keeps the order within each list */
  private static function interleavings(array $lists): array
  {
    $lists = array_values(array_filter($lists));
    if (count($lists) <= 1) {
      return [$lists[0] ?? []];
    }
    $result = [];
    foreach ($lists as $i => $list) {
      $rest = $lists;
      $rest[$i] = array_slice($list, 1);
      foreach (self::interleavings($rest) as $tail) {
        $result[] = array_merge([$list[0]], $tail);
      }
    }
    return array_values(array_unique($result, SORT_REGULAR));
  }
  /** @var array<string, true> tables whose auto-increment may be behind BGA's since the last undo */
  private array $afterUndo = [];

  private function playMove(int $index, int $depth = 0): bool
  {
    [$move, $expected] = $this->moveList[$index];
    Table::setGlobal(3, $move);
    $this->prepareRandomness($move, $expected);
    // An undo (Log::revertTo) deletes from gamelog the notifications of every request from the
    // first one it reverts, including requests of the other player since that point (e.g. their
    // turn confirmation) and its own: only the state it lands in is left. Replay the shortest
    // path to that state: nothing, a confirmation that was erased, or the undo itself.
    $erased = empty($expected) && !empty($this->replay->states[$move]);
    $candidates = $this->candidates($expected);
    if ($erased) {
      // An undo with nothing left to compare would match anything: handled by playErasedMove
      $candidates = array_values(array_filter($candidates, fn($c) => !str_starts_with($c[0], 'actRestart')));
    }
    // A request without notification (a branch choice, a confirmation) only shows its state
    // sequence, which several actions can share: collect all of them
    $matches = $this->findMatches($move, $expected, $candidates, empty($expected));
    if (empty($matches)) {
      if ($erased) {
        return $this->playErasedMove($index, $depth);
      }
      return false;
    }
    if (count($matches) == 1 || $depth >= self::LOOKAHEAD) {
      $this->apply($move, $matches[0]);
      return true;
    }
    // Ambiguous: keep the action after which the next requests replay too
    $snapshot = $this->snapshot($depth);
    foreach ($matches as $match) {
      $this->restore($snapshot);
      $this->apply($move, $match);
      if ($this->lookahead($index + 1, $depth + 1)) {
        $this->restore($snapshot);
        $this->apply($move, $match);
        return true;
      }
    }
    $this->restore($snapshot);
    $this->apply($move, $matches[0]);
    return true;
  }

  private function lookahead(int $from, int $depth): bool
  {
    for ($i = $from; $i < min($from + self::LOOKAHEAD, count($this->moveList)); $i++) {
      if (!$this->playMove($i, $depth)) {
        return false;
      }
    }
    return true;
  }

  /**
   * Tries each candidate in a rolled back transaction.
   * @return array matching candidates (the first one only unless $all)
   */
  private function findMatches(int $move, array $expected, array $candidates, bool $all): array
  {
    $best = null;
    $matches = [];
    // A rolled back INSERT still consumes its auto-increment value: save the counters to restore them
    $counters = $this->counters();
    $dice = $this->dice;
    $this->attempts = [];
    $tries = [];
    foreach ($this->deckVariants as $variant) {
      foreach ($candidates as $candidate) {
        $tries[] = [...$candidate, $variant];
      }
    }
    foreach ($tries as $candidate) {
      [$label] = $candidate;
      $this->dice = $dice;
      BgaDb::conn()->begin_transaction();
      $this->applyDeckOrder($candidate[3]);
      [$produced, $error] = $this->execute($candidate);
      $diff = $error ?? self::diff($expected, $produced) ?? $this->stateDiff($move);
      BgaDb::conn()->rollback();
      $this->restoreCounters($counters);
      if ($diff === null) {
        $matches[] = $candidate;
        if (!$all) {
          break;
        }
        continue;
      }
      $score = $error ? -1 : self::commonPrefix($expected, $produced);
      $this->attempts[] = mb_substr($label, 0, 120) . ' => ' . mb_substr($diff, 0, 300);
      if ($best === null || $score > $best['score']) {
        $best = ['candidate' => mb_substr($label, 0, 200), 'diff' => mb_substr($diff, 0, 1000), 'score' => $score];
      }
    }
    $this->dice = $dice;
    $this->newRequest();
    if (empty($matches)) {
      $this->divergence = [
        'move' => $move,
        'state' => $this->game->gamestate->state(true)['name'],
        'active' => (int) $this->game->getActivePlayerId(),
        'candidates' => count($tries),
        'closest' => $best,
        'expected' => array_map(fn($ns) => array_column($ns, 'type'), $expected),
        'args' => array_diff_key($this->game->gamestate->state()['args'] ?? [], ['previousSteps' => 1]),
        'board' => self::board(),
      ];
    }
    return $matches;
  }

  /** Runs a candidate as a request; returns the notifications grouped by channel, and the error if any */
  private function execute(array $candidate): array
  {
    [, $pId, $fn] = $candidate;
    ob_start(); // the game's autoloader var_dumps a backtrace on unknown classes
    $this->newRequest();
    Table::$notifications = [];
    Table::$lastFlush = null;
    $error = null;
    Table::$currentPlayerId = $pId;
    try {
      $fn($this->game);
    } catch (\Throwable $e) {
      $error = get_class($e) . ': ' . $e->getMessage();
    } finally {
      Table::$currentPlayerId = null;
      ob_end_clean();
    }
    // An undo (Log::revertTo) flushes clearTurn/refreshUI, then deletes from gamelog every
    // notification of the reverted requests and of itself: the replay only keeps what follows.
    if (Table::$lastFlush !== null && in_array('clearTurn', array_column(Table::$notifications, 'name'))) {
      Table::$notifications = array_slice(Table::$notifications, Table::$lastFlush);
    }
    return [$this->channels(Table::$notifications), $error];
  }

  private function apply(int $move, array $candidate): void
  {
    $dice = $this->dice;
    BgaDb::conn()->begin_transaction();
    $this->applyDeckOrder($candidate[3] ?? []);
    $this->execute($candidate);
    BgaDb::conn()->commit();
    $this->dice = $dice;
    $this->newRequest();
    $this->divergence = null;
    if (str_starts_with($candidate[0], 'actRestart') || str_starts_with($candidate[0], 'actUndoToStep')) {
      $this->afterUndo = ['cards' => true, 'meeples' => true];
    }
    $this->played[] = ['move' => $move, 'action' => mb_substr($candidate[0], 0, 200)];
  }

  private function counters(): array
  {
    return BgaDb::conn()->query(
      'SELECT TABLE_NAME, AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND AUTO_INCREMENT IS NOT NULL AND TABLE_NAME NOT LIKE \'snap%\''
    )->fetch_all();
  }

  private function restoreCounters(array $counters): void
  {
    foreach ($counters as [$table, $next]) {
      BgaDb::query("ALTER TABLE `$table` AUTO_INCREMENT = $next");
    }
  }

  /** Copies every game table, and the runner state, to come back to it after a lookahead */
  private function snapshot(int $depth): array
  {
    $tables = array_column(BgaDb::conn()->query("SHOW TABLES WHERE Tables_in_" . (getenv('DB_NAME') ?: 'altered') . " NOT LIKE 'snap%'")->fetch_all(), 0);
    foreach ($tables as $table) {
      BgaDb::query("DROP TABLE IF EXISTS `snap{$depth}_$table`");
      BgaDb::query("CREATE TABLE `snap{$depth}_$table` LIKE `$table`");
      BgaDb::query("INSERT INTO `snap{$depth}_$table` SELECT * FROM `$table`");
    }
    return ['depth' => $depth, 'tables' => $tables, 'counters' => $this->counters(), 'played' => count($this->played), 'afterUndo' => $this->afterUndo, 'dice' => $this->dice];
  }

  private function restore(array $snapshot): void
  {
    foreach ($snapshot['tables'] as $table) {
      BgaDb::query("DELETE FROM `$table`");
      BgaDb::query("INSERT INTO `$table` SELECT * FROM `snap{$snapshot['depth']}_$table`");
    }
    $this->restoreCounters($snapshot['counters']);
    $this->played = array_slice($this->played, 0, $snapshot['played']);
    $this->afterUndo = $snapshot['afterUndo'];
    $this->dice = $snapshot['dice'];
    $this->divergence = null;
    $this->newRequest();
  }

  /**
   * The request and the ones before it were erased from the log by an undo: only the state it
   * lands in is known. Find the short action sequences that reach that state (an erased
   * confirmation, a turn the player really ended, the undo itself) and keep the one after which
   * the next requests replay.
   */
  private function playErasedMove(int $index, int $depth = 0): bool
  {
    [$move] = $this->moveList[$index];
    $target = [end($this->replay->states[$move]), $this->replay->lastActive[$move] ?? 0];
    $sequences = $this->erasedSequences($target, 3);
    if (empty($sequences)) {
      $this->divergence = [
        'move' => $move,
        'state' => $this->game->gamestate->state(true)['name'],
        'active' => (int) $this->game->getActivePlayerId(),
        'candidates' => 0,
        'closest' => ['candidate' => 'erased requests', 'diff' => 'no sequence of up to 3 actions leads to state ' . json_encode($target), 'score' => 0],
        'expected' => [],
      ];
      return false;
    }
    if (count($sequences) == 1 || $depth >= self::LOOKAHEAD) {
      $this->applySequence($move, $sequences[0]);
      return true;
    }
    $snapshot = $this->snapshot($depth);
    foreach ($sequences as $sequence) {
      $this->restore($snapshot);
      $this->applySequence($move, $sequence);
      if ($this->lookahead($index + 1, $depth + 1)) {
        $this->restore($snapshot);
        $this->applySequence($move, $sequence);
        return true;
      }
    }
    $this->restore($snapshot);
    $this->applySequence($move, $sequences[0]);
    return true;
  }

  private function applySequence(int $move, array $sequence): void
  {
    if (empty($sequence)) {
      $this->played[] = ['move' => $move, 'action' => 'erased: no-op (already in the target state)'];
    }
    foreach ($sequence as $candidate) {
      $candidate[0] = 'erased: ' . $candidate[0];
      $this->apply($move, $candidate);
    }
    // Erased requests may have consumed ids too
    $this->afterUndo = ['cards' => true, 'meeples' => true];
  }

  /** Action sequences (shortest first) that bring the game to [state id, active player] */
  private function erasedSequences(array $target, int $maxLength): array
  {
    $reached = function () use ($target) {
      $this->newRequest();
      return [$this->game->gamestate->state_id(), (int) $this->game->getActivePlayerId()] == $target;
    };
    if ($reached()) {
      return [[]];
    }
    $counters = $this->counters();
    $found = [];
    $savepoint = 0;
    $explore = function (array $prefix) use (&$explore, &$found, &$savepoint, $reached, $maxLength) {
      if (count($found) >= 12 || count($prefix) >= $maxLength) {
        return;
      }
      foreach ($this->candidates([]) as $candidate) {
        $name = 'sp' . ++$savepoint;
        BgaDb::query("SAVEPOINT $name");
        [, $error] = $this->execute($candidate);
        if ($error === null) {
          if ($reached()) {
            $found[] = [...$prefix, $candidate];
          } else {
            $explore([...$prefix, $candidate]);
          }
        }
        BgaDb::query("ROLLBACK TO SAVEPOINT $name");
        $this->newRequest();
      }
    };
    BgaDb::conn()->begin_transaction();
    $explore([]);
    BgaDb::conn()->rollback();
    $this->restoreCounters($counters);
    $this->newRequest();
    usort($found, fn($a, $b) => count($a) <=> count($b));
    return $found;
  }

  /** Card ids per player and location, to compare with the replay by hand */
  private static function board(): array
  {
    $board = [];
    foreach (BgaDb::conn()->query("SELECT player_id, card_location, GROUP_CONCAT(card_id ORDER BY card_id) ids FROM cards WHERE card_location NOT LIKE 'deck%' GROUP BY player_id, card_location")->fetch_all() as [$pId, $location, $ids]) {
      $board[$pId][$location] = $ids;
    }
    return $board;
  }

  /** The state ids of our engine must follow the ones recorded on the table channel */
  private function stateDiff(int $move): ?string
  {
    $ours = array_column(array_filter(Table::$notifications, fn($n) => $n['name'] == 'gameStateChange'), 'args');
    $ours = array_map(fn($a) => $a['id'], $ours);
    $theirs = $this->replay->states[$move] ?? [];
    return $ours == $theirs ? null : 'state sequence: replay ' . json_encode($theirs) . ' / local ' . json_encode($ours);
  }

  /** @return array<int, array{0: string, 1: int, 2: callable}> */
  private function candidates(array $expected): array
  {
    $state = $this->game->gamestate->state();
    $name = $state['name'];
    // Args may hold game objects (Collection): work on their JSON form, as the client does
    $args = json_decode(json_encode($state['args'] ?? []), true) ?? [];
    $active = (int) $this->game->getActivePlayerId();
    $out = [];
    // The client sends selected ids in click order, which is the order they show up in the notifications
    $json = json_encode($expected);
    $clickOrder = function (array $actionArgs) use ($json) {
      return array_map(function ($arg) use ($json) {
        if (!is_array($arg) || !array_is_list($arg)) {
          return $arg;
        }
        $pos = fn($id) => ($p = strpos($json, '"id": ' . $id) ?: strpos($json, '"id":' . $id)) === false ? PHP_INT_MAX : $p;
        usort($arg, fn($a, $b) => $pos($a) <=> $pos($b));
        return $arg;
      }, $actionArgs);
    };
    $atomic = function (string $action, array $actionArgs, ?int $pId = null) use (&$out, $active, $clickOrder) {
      $actionArgs = $clickOrder($actionArgs);
      // Notifications sometimes keep the click order only in a log string: try the other orders
      foreach ($actionArgs as $i => $arg) {
        if (is_array($arg) && array_is_list($arg) && count($arg) > 1 && count($arg) <= 4) {
          foreach (array_slice(self::permutations($arg), 1) as $perm) {
            $permArgs = $actionArgs;
            $permArgs[$i] = $perm;
            $out[] = ["$action " . json_encode($permArgs), $pId ?? $active, fn($g) => $g->actTakeAtomicAction($action, $permArgs)];
          }
        }
      }
      $out[] = ["$action " . json_encode($actionArgs), $pId ?? $active, fn($g) => $g->actTakeAtomicAction($action, $actionArgs)];
    };
    $call = function (string $method, array $methodArgs, ?int $pId = null) use (&$out, $active) {
      $out[] = ["$method " . json_encode($methodArgs), $pId ?? $active, fn($g) => $g->$method(...$methodArgs)];
    };

    // Multi-active states: the acting player is the one receiving the private update
    $actor = $active;
    foreach ($expected as $channel => $notifs) {
      if ($channel !== 'table') {
        $actor = $channel;
        break;
      }
    }

    switch ($name) {
      case 'selectPrecoDeck':
        foreach ($this->replay->players as $pId) {
          $call('actConfirmAPIDeck', [$this->replay->apiDeck($pId)], $pId);
        }
        break;
      case 'firstDayManaSelection':
        foreach ($expected as $channel => $notifs) {
          foreach ($notifs as $n) {
            if ($n['type'] == 'updateFirstDayManaSelection') {
              $call('actFirstDayManaSelection', [$n['args']['args']['_private']['selection']], $channel);
            }
          }
        }
        break;
      case 'chooseAssignment':
        $private = $args['_private'][$active] ?? $args['_private']['active'] ?? [];
        foreach ($private['play'] ?? [] as $cardId => $locations) {
          foreach ($locations as $location) {
            $atomic('actPlay', [$cardId, $location]);
          }
        }
        foreach ($private['support'] ?? [] as $cardId) {
          $atomic('actSupport', [$cardId]);
        }
        foreach ($private['tap'] ?? [] as $cardId) {
          $atomic('actTap', [$cardId]);
        }
        $atomic('actPass', []);
        break;
      case 'target':
        $ids = $args['cardIds'] ?? [];
        $sizes = ($args['upTo'] ?? false) ? range(0, $args['n']) : [min($args['n'], count($ids))];
        foreach ($sizes as $size) {
          foreach (self::combinations($ids, $size) as $combo) {
            $atomic('actTarget', [$combo]);
          }
        }
        break;
      case 'discard':
        foreach (self::intLists($args) as $ids) {
          foreach ($ids as $id) {
            $atomic('actDiscard', [[$id]]);
          }
        }
        $atomic('actDiscard', [[]]);
        break;
      case 'playCard':
        // Playing a card through an effect (free play...): same map as chooseAssignment
        foreach (($args['_private'][$active] ?? $args['_private']['active'] ?? [])['play'] ?? [] as $cardId => $locations) {
          foreach ($locations as $location) {
            $atomic('actPlayCard', [$cardId, $location]);
          }
        }
        break;
      case 'markRegion':
        // Marker and region ids are the keys of the args
        foreach (array_keys($args['markers'] ?? []) as $markerId) {
          foreach (array_keys($args['regions'] ?? []) as $stormId) {
            $atomic('actMarkRegion', [$markerId, $stormId]);
          }
        }
        break;
      case 'targetExpedition':
        foreach ($args['expeditions'] ?? [] as $expedition) {
          $atomic('actTargetExpedition', [$expedition]);
        }
        foreach (range(1, max(1, $args['n'] ?? 1)) as $size) {
          foreach (self::combinations($args['expeditions'] ?? [], $size) as $combo) {
            $atomic('actTargetExpedition', [$combo]);
          }
        }
        $atomic('actTargetExpedition', ['all']);
        break;
      case 'invokeToken':
        foreach ($args['locations'] ?? [] as $location) {
          $atomic('actInvokeToken', [$location]);
          // allPlayers: the client appends the owner of the chosen expedition
          if ($args['allPlayers'] ?? false) {
            foreach ($this->replay->players as $pId) {
              $atomic('actInvokeToken', ["$location-$pId"]);
            }
          }
        }
        $atomic('actInvokeTokenPass', []);
        break;
      case 'spend':
        for ($n = 0; $n <= ($args['n'] ?? 0); $n++) {
          $atomic('actSpend', [$n]);
        }
        break;
      case 'nightCleanup':
        $private = $args['_private'][$active] ?? $args['_private']['active'] ?? [];
        // Exactly nReserve / nLandmarks cards to discard
        foreach (self::combinations($private['reserveCards'] ?? [], $args['nReserve'] ?? 0) as $reserve) {
          foreach (self::combinations($private['landmarkCards'] ?? [], $args['nLandmarks'] ?? 0) as $landmarks) {
            $atomic('actNightCleanup', [$reserve, $landmarks]);
          }
        }
        break;
      case 'resolveChoice':
        foreach ($args['choices'] ?? [] as $choice) {
          $call('actChooseAction', [$choice['id']]);
        }
        break;
      case 'confirmTurn':
        $call('actConfirmTurn', []);
        break;
      case 'confirmPartialTurn':
        $call('actConfirmPartialTurn', []);
        break;
      default:
        // Any other atomic action: try its act* methods, with no argument or one id from the args
        $node = \ALT\Core\Engine::getNextUnresolved();
        if ($node && $node->getAction()) {
          $action = \ALT\Managers\Actions::get($node->getAction(), $node);
          $ids = array_unique(array_merge(...array_merge([[]], self::intLists($args), self::stringLists($args))));
          foreach ((new \ReflectionClass($action))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!str_starts_with($method->name, 'act') || $method->class == \ALT\Models\Action::class) {
              continue;
            }
            if ($method->getNumberOfRequiredParameters() == 0) {
              $atomic($method->name, []);
            } elseif ($method->getNumberOfRequiredParameters() == 1) {
              foreach ($ids as $id) {
                $atomic($method->name, [$id]);
                $atomic($method->name, [[$id]]);
              }
            } elseif ($method->getNumberOfRequiredParameters() == 2) {
              // e.g. actPlayCard(cardId, location), actMarkRegion(markerId, stormId)
              foreach ($ids as $first) {
                foreach ($ids as $second) {
                  if ($first !== $second) {
                    $atomic($method->name, [$first, $second]);
                  }
                }
              }
            }
          }
        }
    }
    $call('actPassOptionalAction', []);
    $call('actRestart', []);

    // Try first the candidates whose ids all appear in the recorded notifications
    usort($out, function ($a, $b) use ($json) {
      return self::hintScore($b[0], $json) <=> self::hintScore($a[0], $json);
    });
    return $out;
  }

  private static function hintScore(string $label, string $json): int
  {
    preg_match_all('/\d{2,}/', $label, $m);
    if (empty($m[0])) {
      return 0;
    }
    foreach ($m[0] as $id) {
      if (!str_contains($json, (string) $id)) {
        return -1;
      }
    }
    return count($m[0]);
  }

  private static function permutations(array $items): array
  {
    if (count($items) <= 1) {
      return [$items];
    }
    $result = [];
    foreach ($items as $i => $item) {
      $rest = $items;
      unset($rest[$i]);
      foreach (self::permutations(array_values($rest)) as $perm) {
        $result[] = array_merge([$item], $perm);
      }
    }
    return $result;
  }

  private static function stringLists(array $args): array
  {
    $lists = [];
    $visit = function ($node) use (&$visit, &$lists) {
      if (!is_array($node)) {
        return;
      }
      if (!empty($node) && array_is_list($node) && count(array_filter($node, 'is_string')) == count($node) && count($node) <= 12) {
        $lists[] = $node;
      }
      foreach ($node as $child) {
        $visit($child);
      }
    };
    $visit($args);
    return $lists;
  }

  private static function intLists(array $args): array
  {
    $lists = [];
    array_walk_recursive($args, function () {});
    $visit = function ($node) use (&$visit, &$lists) {
      if (!is_array($node)) {
        return;
      }
      if (!empty($node) && array_is_list($node) && count(array_filter($node, 'is_numeric')) == count($node)) {
        $lists[] = $node;
      }
      foreach ($node as $child) {
        $visit($child);
      }
    };
    $visit($args);
    return $lists;
  }

  private static function combinations(array $items, int $k): array
  {
    if ($k == 0) {
      return [[]];
    }
    if (count($items) < $k || count($items) > 12) {
      return count($items) < $k ? [] : array_map(fn($i) => [$i], array_slice($items, 0, 50));
    }
    $result = [];
    foreach ($items as $i => $item) {
      foreach (self::combinations(array_slice($items, $i + 1), $k - 1) as $rest) {
        $result[] = array_merge([$item], $rest);
      }
    }
    return $result;
  }


  private function newRequest(): void
  {
    foreach ([Cards::class, Meeples::class, Players::class] as $manager) {
      $manager::invalidate();
    }
    $reset = fn($class, $prop, $value) => (new \ReflectionProperty($class, $prop))->setValue(null, $value);
    $reset(Globals::class, 'initialized', false);
    $reset(Globals::class, 'data', []);
    $reset(Notifications::class, 'cachedValues', []);
    Table::harnessResetGlobalsCache();
    $this->game = new altered();
  }

  /** Group produced notifications like the replay: per channel, ignored types removed */
  private function channels(array $notifs): array
  {
    $out = [];
    foreach ($notifs as $n) {
      if (in_array($n['name'], Replay::IGNORED)) {
        continue;
      }
      $out[$n['target'] ?? 'table'][] = ['type' => $n['name'], 'args' => json_decode(json_encode($n['args']), true)];
    }
    return $out;
  }

  public static function normalize($value)
  {
    if (is_array($value)) {
      unset($value['i18n'], $value['preserve']);
      return array_map([self::class, 'normalize'], $value);
    }
    if (is_string($value) && is_numeric($value)) {
      return $value + 0;
    }
    return $value;
  }

  /** null when equal, otherwise a description of the first difference */
  public static function diff(array $expected, array $produced): ?string
  {
    foreach (array_unique(array_merge(array_keys($expected), array_keys($produced))) as $channel) {
      $e = $expected[$channel] ?? [];
      $p = $produced[$channel] ?? [];
      for ($i = 0; $i < max(count($e), count($p)); $i++) {
        if (!isset($e[$i]) || !isset($p[$i])) {
          $extra = $e[$i] ?? $p[$i];
          return "[$channel] notification #$i " . (isset($e[$i]) ? 'missing locally' : 'extra locally') . ": {$extra['type']}";
        }
        $path = self::firstDiff(self::normalize($e[$i]), self::normalize($p[$i]), '');
        if ($path !== null) {
          return "[$channel] notification #$i ({$e[$i]['type']}) differs at $path";
        }
      }
    }
    return null;
  }

  private static function firstDiff($a, $b, string $path): ?string
  {
    if (is_array($a) && is_array($b)) {
      foreach (array_unique(array_merge(array_keys($a), array_keys($b))) as $k) {
        if (!array_key_exists($k, $a) || !array_key_exists($k, $b)) {
          return "$path.$k (" . (array_key_exists($k, $a) ? 'missing locally' : 'extra locally') . ')';
        }
        if (($d = self::firstDiff($a[$k], $b[$k], "$path.$k")) !== null) {
          return $d;
        }
      }
      return null;
    }
    return $a === $b ? null : "$path: replay=" . json_encode($a) . ' local=' . json_encode($b);
  }

  private static function commonPrefix(array $expected, array $produced): int
  {
    $score = 0;
    foreach ($expected as $channel => $e) {
      foreach ($e as $i => $n) {
        if (isset($produced[$channel][$i]) && $produced[$channel][$i]['type'] == $n['type']) {
          $score++;
          if (self::firstDiff(self::normalize($n), self::normalize($produced[$channel][$i]), '') === null) {
            $score++;
          }
        }
      }
    }
    return $score;
  }
}
