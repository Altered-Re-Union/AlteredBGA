<?php

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Plays every card from hand on a standard board and resolves its effect with a naive
 * player (first choice, first targets, pass optional steps). Fails on any exception;
 * records the number of SQL queries per card in /harness/sweep.csv.
 *
 * SWEEP=all runs every card, SWEEP=<n> an evenly spread sample of n cards (default 40),
 * SWEEP=<regex> the cards whose class name matches.
 * SWEEP_PICK=last makes the naive player take the last choice and the last targets instead.
 *
 * Each card also gets a fingerprint (SWEEP_FINGERPRINTS file): a hash of every notification
 * sent and one of the final database, so that two versions of the code can be compared card by
 * card (tools/compare_sweep.py). SWEEP_DUMP=1 keeps the notifications and database of each card
 * in results/dump/ to look at a difference.
 */
class CardSweepTest extends AlteredTestCase
{
  private const MAX_STEPS = 40;

  /**
   * Every card starts from the same game: it is set up once per process (game creation, deck
   * and mana selection, standard board), saved as a snapshot, and restored before each card.
   * Runs in one process per shard (phpunit-sweep.xml): newRequest() drops the game's static
   * caches, as between two requests on BGA.
   */
  private static ?array $board = null;

  protected function setUp(): void
  {
    if (self::$board === null) {
      parent::setUp();
      $this->startGame();
      $me = $this->activeId();
      $opp = $me == self::P1 ? self::P2 : self::P1;
      $this->createCard($opp, 'AX_Common_IraFairAttendee', STORM_LEFT);
      $this->createCard($opp, 'BR_Common_Kappa', STORM_RIGHT);
      $this->createCard($me, 'BR_Common_DevotedProtector', STORM_LEFT);
      for ($i = 0; $i < 9; $i++) {
        $this->createCard($me, 'BR_Common_Kappa', MANA);
      }
      $this->readyMana($me);
      BgaDb::saveSnapshot('board');
      self::$board = ['me' => $me, 'opp' => $opp];
    }
    BgaDb::restoreSnapshot('board');
    Table::$notifications = [];
    Table::$currentPlayerId = null;
    BgaDb::$evaluationWrites = [];
    mt_srand(42);
    srand(42);
    $this->newRequest();
  }

  public static function cards(): array
  {
    $files = glob(getenv('GAME_DIR') . '/modules/php/Cards/*/*.php');
    $classes = array_map(fn($f) => basename($f, '.php'), $files);
    $classes = array_values(array_filter($classes, fn($c) => preg_match('/^[A-Z]{2}_(Common|Rare|Unique|Exalted)_/', $c)));
    sort($classes);
    $sweep = getenv('SWEEP') ?: '40';
    if ($sweep !== 'all') {
      if (ctype_digit($sweep)) {
        $step = max(1, intdiv(count($classes), (int) $sweep));
        $classes = array_values(array_filter($classes, fn($i) => $i % $step == 0, ARRAY_FILTER_USE_KEY));
      } else {
        $classes = array_values(array_filter($classes, fn($c) => preg_match("/$sweep/", $c)));
      }
    }
    return array_combine($classes, array_map(fn($c) => [$c], $classes));
  }

  #[DataProvider('cards')]
  public function testPlayFromHand(string $class): void
  {
    $me = self::$board['me'];
    try {
      $cardId = $this->createCard($me, $class, HAND);
    } catch (\Throwable $e) {
      $this->markTestSkipped("cannot instantiate: {$e->getMessage()}");
    }

    $play = $this->game->argsAtomicAction()['_private'][$me]['play'] ?? [];
    if (!isset($play[$cardId])) {
      $this->markTestSkipped('not playable from hand on the standard board');
    }

    $outcome = 'error';
    try {
      $cost = $this->measure(function () use ($cardId, $play, $me) {
        $this->act('actPlay', $cardId, $play[$cardId][0]);
        $this->resolveNaively($me);
      });
      $outcome = 'resolved';
    } catch (\PHPUnit\Framework\IncompleteTestError $e) {
      $outcome = 'incomplete';
      throw $e;
    } finally {
      $this->fingerprint($class, $outcome);
    }
    file_put_contents(getenv('SWEEP_OUT') ?: '/harness/sweep.csv', "$class,{$cost['queries']},{$cost['totalMs']}\n", FILE_APPEND);
  }

  private function fingerprint(string $class, string $outcome): void
  {
    $notifications = json_encode(Table::$notifications);
    $db = [];
    foreach (['cards', 'meeples', 'global_variables', 'player', 'stats', 'user_preferences'] as $table) {
      $db[$table] = BgaDb::conn()->query("SELECT * FROM `$table` ORDER BY 1")->fetch_all(MYSQLI_ASSOC);
    }
    $db = json_encode($db);
    $line = implode(',', [$class, getenv('SWEEP_PICK') ?: 'first', $outcome, md5($notifications), md5($db)]);
    file_put_contents(getenv('SWEEP_FINGERPRINTS') ?: '/harness/results/fingerprints.csv', "$line\n", FILE_APPEND);
    if (getenv('SWEEP_DUMP')) {
      @mkdir('/harness/results/dump', 0777, true);
      file_put_contents("/harness/results/dump/$class.json", json_encode(['notifications' => Table::$notifications, 'db' => json_decode($db, true)], JSON_PRETTY_PRINT));
    }
    // Writes made while the engine was evaluating choices (see BgaDb::$evaluationWrites)
    foreach (BgaDb::$evaluationWrites as $write) {
      file_put_contents('/harness/results/evaluation_writes.txt', "$class: $write\n", FILE_APPEND);
    }
  }

  /** Answer every pending decision until the turn goes back to a chooseAssignment */
  private function resolveNaively(int $me): void
  {
    for ($step = 0; $step < self::MAX_STEPS; $step++) {
      $state = $this->game->gamestate->state();
      $name = $state['name'];
      $args = $state['args'] ?? [];
      if ($name == 'chooseAssignment' || $name == 'gameEnd') {
        $this->addToAssertionCount(1);
        return;
      }
      if ($name == 'confirmTurn' || $name == 'confirmPartialTurn') {
        $this->as($this->activeId(), fn($g) => $g->actConfirmTurn());
      } elseif ($name == 'resolveChoice') {
        $choices = array_values($args['choices']);
        $choice = getenv('SWEEP_PICK') == 'last' ? end($choices) : $choices[0];
        $this->chooseChoice($choice['id']);
      } elseif ($name == 'target') {
        if (getenv('SWEEP_PICK') == 'last') {
          // The last targets, as many as allowed: fewer if the effect refuses them (total cost...)
          $n = min($args['n'], count($args['cardIds']));
          for ($k = $n; $k >= ($args['upTo'] ? 0 : $n); $k--) {
            try {
              $this->act('actTarget', $k ? array_slice($args['cardIds'], -$k) : []);
              break;
            } catch (\BgaUserException $e) {
              if ($k == ($args['upTo'] ? 0 : $n)) {
                throw $e;
              }
            }
          }
        } else {
          $n = $args['upTo'] ? 0 : min($args['n'], count($args['cardIds']));
          $this->act('actTarget', array_slice($args['cardIds'], 0, $n));
        }
      } elseif (($args['optionalAction'] ?? false) === true) {
        $this->as($this->activeId(), fn($g) => $g->actPassOptionalAction());
      } else {
        $this->markTestIncomplete("naive player cannot answer state '$name'");
      }
    }
    $this->fail('effect did not resolve within ' . self::MAX_STEPS . ' decisions');
  }
}
