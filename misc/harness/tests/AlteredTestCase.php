<?php

use ALT\Core\Engine;
use ALT\Core\Globals;
use ALT\Core\Notifications;
use ALT\Managers\Cards;
use ALT\Managers\Meeples;
use ALT\Managers\Players;
use PHPUnit\Framework\TestCase;

abstract class AlteredTestCase extends TestCase
{
  public const P1 = 2001;
  public const P2 = 2002;

  protected altered $game;

  protected function setUp(): void
  {
    BgaDb::resetDatabase(GAME_DIR);
    Table::harnessResetGlobalsCache();
    Table::$notifications = [];
    mt_srand(42);
    srand(42);
    $this->game = altered::harnessCreate([self::P1 => 'Alice', self::P2 => 'Bob']);
  }

  protected function tearDown(): void
  {
    // PHPUnit serializes the test object back to the parent process: drop the game graph
    unset($this->game);
  }

  protected function location(int $cardId): string
  {
    Cards::invalidate();
    return Cards::get($cardId)->getLocation();
  }

  /** @return int[] ids of the cards of a given class owned by $pId in $location */
  protected function cardsOf(int $pId, string $location, string $class): array
  {
    Cards::invalidate();
    return Cards::getFiltered($pId, $location)
      ->filter(fn($c) => (new \ReflectionClass($c))->getShortName() == $class)
      ->getIds();
  }

  /**
   * BGA serves each action in a fresh PHP process: drop every static cache and rebuild
   * the game object, so state only survives through the database.
   */
  protected function newRequest(): void
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

  /** Run $fn as a new request sent by $pId */
  protected function as(int $pId, callable $fn)
  {
    $this->newRequest();
    Table::$currentPlayerId = $pId;
    try {
      return $fn($this->game);
    } finally {
      Table::$currentPlayerId = null;
    }
  }

  protected function stateName(): string
  {
    return $this->game->gamestate->state(true)['name'];
  }

  protected function args(): array
  {
    return $this->game->gamestate->state()['args'] ?? [];
  }

  protected function activeId(): int
  {
    return (int) $this->game->getActivePlayerId();
  }

  /**
   * Both players pick their first starter deck and the first 3 cards of their hand as mana:
   * the game then waits for the first player's assignment.
   */
  protected function startGame(): void
  {
    $decks = Globals::getPlayerDecks();
    foreach ([self::P1, self::P2] as $pId) {
      $this->as($pId, fn($g) => $g->actSelectPrecoDeck(array_key_first($decks[$pId])));
    }
    $args = $this->game->argsFirstDayManaSelection();
    foreach ([self::P1, self::P2] as $pId) {
      $cards = array_slice($args['_private'][$pId]['cards'], 0, 3);
      $this->as($pId, fn($g) => $g->actFirstDayManaSelection($cards));
    }
  }

  /**
   * Create a card for a player directly in a location (hand, reserve, stormLeft, landmark...).
   * @param string $class e.g. 'OD_Rare_AngryCrowd'
   */
  protected function createCard(int $pId, string $class, string $location = HAND): int
  {
    $faction = substr($class, 0, 2);
    $className = "\\ALT\\Cards\\$faction\\$class";
    $card = new $className(null);
    $created = Cards::singleCreate([
      'player_id' => $pId,
      'location' => $location,
      'nbr' => 1,
      'properties' => $card->getProperties(),
    ]);
    Cards::invalidate();
    return (int) $created->getId();
  }

  /** Same entry point as the client's ajax call for engine actions */
  protected function act(string $actionName, ...$args)
  {
    $this->newRequest();
    return $this->as($this->activeId(), fn($g) => $g->actTakeAtomicAction($actionName, $args));
  }

  protected function chooseChoice(int $choiceId)
  {
    return $this->as($this->activeId(), fn($g) => $g->actChooseAction($choiceId));
  }

  /** Give mana to a player by readying all their mana orbs */
  protected function readyMana(int $pId): void
  {
    BgaDb::query("UPDATE cards SET card_state = 0 WHERE player_id = $pId AND card_location = '" . MANA . "'");
    Cards::invalidate();
  }

  /** Debug output: tests run in separate processes, so write to a file */
  protected function log(string $msg): void
  {
    file_put_contents('/harness/out.log', $msg . "\n", FILE_APPEND);
  }

  /** Measure DB queries and wall time of $fn */
  protected function measure(callable $fn, bool $profile = false): array
  {
    BgaDb::$queryCount = 0;
    BgaDb::$queryTime = 0;
    BgaDb::$profile = $profile ? [] : null;
    $t = microtime(true);
    $fn();
    $result = [
      'queries' => BgaDb::$queryCount,
      'dbMs' => round(BgaDb::$queryTime * 1000),
      'totalMs' => round((microtime(true) - $t) * 1000),
    ];
    if ($profile) {
      arsort(BgaDb::$profile);
      $result['top'] = array_slice(BgaDb::$profile, 0, 10, true);
      BgaDb::$profile = null;
    }
    return $result;
  }
}
