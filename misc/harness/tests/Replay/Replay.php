<?php

/**
 * A BGA replay as returned by the "logs" request of a finished table (both players' channels):
 * the notifications of every request, grouped by move id.
 */
class Replay
{
  /**
   * Notifications that depend on the BGA plumbing, not on the game logic. newUndoableStep is
   * removed from gamelog by the game once a turn is confirmed (clearUndoableStepNotifications);
   * simpleNode and wakeupPlayers are sent by the framework.
   */
  public const IGNORED = ['gameStateChange', 'gameStateMultipleActiveUpdate', 'updateReflexionTime', 'newUndoableStep', 'simpleNode', 'wakeupPlayers'];

  /** @var int[] player ids in seat order */
  public array $players;
  /** @var array<int, array<string, array>> move id => channel ('table' or player id) => notifications */
  public array $moves = [];
  /** @var array<int, array{0: int, 1: int|string, 2: array}> every notification in packet order: [move, channel, notif] */
  private array $ordered = [];
  /** @var array<int, int[]> move id => state ids sent on the table channel */
  public array $states = [];
  /** @var array<int, int> move id => active player of the last state sent on the table channel */
  public array $lastActive = [];
  /** @var array<int, true> players who got a confirmTurn */
  private array $confirmers = [];
  /** @var array<int, true> players who got a confirmPartialTurn, which needs the undo preference on */
  private array $partialConfirmers = [];

  public function __construct(public string $file)
  {
    $data = json_decode(file_get_contents($file), true)['data'];
    $this->players = array_map(fn($p) => (int) $p['id'], $data['players']);
    $logs = $data['logs'];
    usort($logs, fn($a, $b) => (int) $a['packet_id'] <=> (int) $b['packet_id']);
    foreach ($logs as $packet) {
      if ($packet['move_id'] === null) {
        continue; // framework packets outside any request (wakeupPlayers)
      }
      $move = (int) $packet['move_id'];
      $channel = str_starts_with($packet['channel'], '/player/p') ? (int) substr($packet['channel'], 9) : 'table';
      $this->moves[$move] ??= []; // a request may only change the state (e.g. a confirmation)
      foreach ($packet['data'] as $notif) {
        if ($notif['type'] == 'gameStateChange' && $channel == 'table' && isset($notif['args']['id'])) {
          $this->states[$move][] = (int) $notif['args']['id'];
          $this->lastActive[$move] = (int) ($notif['args']['active_player'] ?? 0);
          if ((int) $notif['args']['id'] == ST_CONFIRM_PARTIAL_TURN) {
            $this->partialConfirmers[(int) $notif['args']['active_player']] = true;
          }
          if ((int) $notif['args']['id'] == ST_CONFIRM_TURN) {
            $this->confirmers[(int) $notif['args']['active_player']] = true;
          }
        }
        if (in_array($notif['type'], self::IGNORED)) {
          continue;
        }
        $this->moves[$move][$channel][] = ['type' => $notif['type'], 'args' => $notif['args']];
        $this->ordered[] = [$move, $channel, ['type' => $notif['type'], 'args' => $notif['args']]];
      }
    }
    ksort($this->moves);
  }

  /**
   * Player preferences are synced by the BGA client and not part of the replay. The "Undo"
   * preference shows in the states: a partial confirmation needs it explicitly enabled, and a
   * player who never confirmed a turn has it disabled. Otherwise it is left unset.
   * @return array<int, array<int, int>> player id => pref id => value
   */
  public function preferences(): array
  {
    $prefs = [];
    foreach ($this->players as $pId) {
      if (isset($this->partialConfirmers[$pId])) {
        $prefs[$pId][OPTION_PLAYER_UNDO] = OPTION_PLAYER_UNDO_ENABLED;
      } elseif (!isset($this->confirmers[$pId])) {
        $prefs[$pId][OPTION_PLAYER_UNDO] = OPTION_PLAYER_UNDO_DISABLED;
      }
    }
    return $prefs;
  }

  /**
   * Token art chosen on the deck builder (Globals tokenStyles). It is set by actGetDeckInfos,
   * which reads the Altered API and sends no notification, so it is rebuilt from the tokens seen.
   * @return array<int, array<string, array>> player id => token class => style
   */
  public function tokenStyles(): array
  {
    $styles = [];
    foreach ($this->all() as [, , $n]) {
      if ($n['type'] != 'invokeToken' || !isset($n['args']['card']['properties']['uid'])) {
        continue;
      }
      $card = $n['args']['card'];
      $class = (new \ReflectionClass(\ALT\Managers\Cards::getCardClass($card['properties']['uid'])))->getShortName();
      $style = ['asset' => $card['properties']['asset']];
      foreach (['mainAsset', 'fullArt'] as $key) {
        if (isset($card['properties'][$key])) {
          $style[$key] = $card['properties'][$key];
        }
      }
      // The style is the invoking player's choice, whoever ends up owning the token
      $styles[(int) ($n['args']['player_id'] ?? $card['pId'])][$class] ??= $style;
    }
    return $styles;
  }

  /**
   * Smallest card / meeple id that shows up for the first time in each request. Rows created then
   * reverted by an undo still consumed auto-increment values on BGA, which this lets us catch up on.
   * @return array<int, array{cards?: int, meeples?: int}> move id => table => id
   */
  public function firstNewIds(): array
  {
    $seen = ['cards' => [], 'meeples' => []];
    $result = [];
    $visit = function ($node, int $move) use (&$visit, &$seen, &$result) {
      if (!is_array($node)) {
        return;
      }
      if (isset($node['id'], $node['location'])) {
        $table = isset($node['properties']) ? 'cards' : (isset($node['type']) ? 'meeples' : null);
        $id = (int) $node['id'];
        if ($table && !isset($seen[$table][$id])) {
          $seen[$table][$id] = true;
          $result[$move][$table] = min($result[$move][$table] ?? PHP_INT_MAX, $id);
        }
      }
      foreach ($node as $child) {
        $visit($child, $move);
      }
    };
    foreach ($this->ordered as [$move, , $n]) {
      $visit($n['args'], $move);
    }
    return $result;
  }

  public function names(): array
  {
    $data = json_decode(file_get_contents($this->file), true)['data'];
    $names = [];
    foreach ($data['players'] as $p) {
      $names[(int) $p['id']] = $p['name'];
    }
    return $names;
  }

  /** Every notification of the replay in packet order (across channels), with its move and channel */
  public function all(): array
  {
    return $this->ordered;
  }

  /** Deck sent by a player through actConfirmAPIDeck (stored in the private args of the deck selection) */
  public function apiDeck(int $pId): ?array
  {
    foreach ($this->all() as [, $channel, $n]) {
      if ($channel === $pId && $n['type'] == 'updateInitialPrecoDeckSelection') {
        return $n['args']['args']['_private']['API'] ?? null;
      }
    }
    return null;
  }

  /** Storm layout revealed during the game: storm index (1..3) => [cardId, rotated] */
  public function storm(): array
  {
    $storm = [];
    foreach ($this->all() as [, , $n]) {
      if ($n['type'] == 'moveStormToken' && !empty($n['args']['revealed'])) {
        $storm[(int) $n['args']['stormIndex']] = [(int) $n['args']['revealed']['cardId'], (bool) $n['args']['revealed']['rotated']];
      }
    }
    ksort($storm);
    return $storm;
  }

  /**
   * Card ids of a player in the order they first show up. A card of the deck is hidden until it
   * leaves the deck, so this is the draw order (for cards that were drawn at all).
   */
  /**
   * Cards of a player first shown in one request, one list per channel. Packets of different
   * channels are not in chronological order within a request, so only each list's order is known.
   * @return array<int|string, int[]> channel => card ids
   */
  public function revealedIn(int $pId, int $move): array
  {
    $before = array_flip($this->revealOrderUntil($pId, $move));
    $lists = [];
    $visit = function ($node, $channel) use (&$visit, &$lists, $pId, $before) {
      if (!is_array($node)) {
        return;
      }
      if (isset($node['id'], $node['location'], $node['pId'], $node['properties']) && (int) $node['pId'] == $pId
        && !str_starts_with($node['location'], 'board-hero') && !str_starts_with((string) $node['location'], 'deck')
        && !isset($before[(int) $node['id']]) && !in_array((int) $node['id'], $lists[$channel] ?? [])) {
        $lists[$channel][] = (int) $node['id'];
      }
      foreach ($node as $child) {
        $visit($child, $channel);
      }
    };
    foreach ($this->ordered as [$m, $channel, $n]) {
      if ($m == $move) {
        $visit($n['args'], $channel);
      }
    }
    return $lists;
  }

  /** Cards of a player shown in requests before $move */
  private function revealOrderUntil(int $pId, int $move): array
  {
    return $this->revealOrder($pId, 0, $move);
  }

  public function revealOrder(int $pId, int $fromMove = 0, int $toMove = PHP_INT_MAX): array
  {
    $order = [];
    $visit = function ($node) use (&$visit, &$order, $pId) {
      if (!is_array($node)) {
        return;
      }
      if (isset($node['id'], $node['location'], $node['pId'], $node['properties']) && (int) $node['pId'] == $pId) {
        if (!str_starts_with($node['location'], 'board-hero') && !str_starts_with((string) $node['location'], 'deck')) {
          $order[(int) $node['id']] ??= count($order);
        }
      }
      foreach ($node as $child) {
        $visit($child);
      }
    };
    foreach ($this->all() as [$move, , $n]) {
      if ($move >= $fromMove && $move < $toMove) {
        $visit($n['args']);
      }
    }
    asort($order);
    return array_keys($order);
  }
}
