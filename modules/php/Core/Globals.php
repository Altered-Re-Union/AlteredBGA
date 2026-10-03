<?php

namespace ALT\Core;

use ALT\Core\Game;
use ALT\Helpers\Utils;

/*
 * Globals
 */

class Globals extends \ALT\Helpers\DB_Manager
{
  protected static $initialized = false;
  protected static $variables = [
    'engine' => 'obj', // DO NOT MODIFY, USED IN ENGINE MODULE
    'engineChoices' => 'int', // DO NOT MODIFY, USED IN ENGINE MODULE
    'engineAutomatic' => 'int', // DO NOT MODIFY, USED IN ENGINE MODULE
    'callbackEngineResolved' => 'obj', // DO NOT MODIFY, USED IN ENGINE MODULE
    'anytimeRecursion' => 'int', // DO NOT MODIFY, USED IN ENGINE MODULE
    'customTurnOrders' => 'obj', // DO NOT MODIFY, USED FOR CUSTOM TURN ORDER FEATURE

    'playerDecks' => 'obj',
    'deckOptions' => 'str',
    'deckContent' => 'obj',
    'undo' => 'bool',
    'statMapping' => 'obj',
    'deckFormat' => 'str',

    'firstPlayer' => 'int',
    'skippedPlayers' => 'obj',
    'dayPhase' => 'bool',
    'activePId' => 'int',
    'zombie' => 'bool',

    'storm' => 'obj',
    'day' => 'int',
    'phase' => 'int',
    'heroes' => 'obj',
    'playedCards' => 'int',
    'stormMoves' => 'obj',
    'expeditionMoves' => 'obj',
    'blockedExpeditions' => 'obj', // pId => StormLet/Right
    'tieBreakerMode' => 'bool',
    'enterTieBreakerMode' => 'bool',
    'instantWin' => 'bool',

    'diceRolls' => 'obj',

    'firstDayManaSelection' => 'obj',
    'nightSelection' => 'obj',
    'deckSelection' => 'obj',
    'costReduction' => 'obj', // pId => character => cost
    'nextCharacterBoost' => 'int',
    'nextCharacterBoostOccurence' => 'int',
    'nextReserveCharacterBoost' => 'int',
    'nextCharacterCost3Anchored' => 'bool',
    'nextCharacterAnchored' => 'bool',
    'nextCharacterFleeting' => 'bool',
    'additionalEffect' => 'obj', // use to trigger R/H of next character
    'afterRest' => 'obj', // used for triggers readd during dusk but that should be trigerred after Rest
    'afterNightCleanup' => 'obj', // used to process the reactions after all players cleaned up their reserve & landmark
    'nextSpellIsFree' => 'bool',
    'removeFleetingIfSpellPlayedHand' => 'bool',
    'removeFleetingSpellPlayed' => 'bool',
    'removeFleetingCharacterPlayed' => 'bool',
    'playedForFree' => 'bool',
    'nextTokenAnchored' => 'bool', // Icebound Taiga
    'removeFleetingCharacterStat0Played' => 'bool', // Bise - The stage
    'removeFleetingSongArtistPlayed' => 'bool', // Bise - The stage rare
    'nextCharacterBoostV' => 'int', // Bise - The Undergrowth

    // Cyclone
    'globalTough' => 'obj', // 'Kauri's intervention
    'nextTokenAsleep' => 'bool', // Pan

    // Duster
    'turnCards' => 'obj',
    'nextCharacterInExpeditionBoost' => 'obj',
    'nextCharacterBaseCost3Anchored' => 'bool',

    // Fugue
    'constructionPlayedThisDay' => 'obj', // pId => bool
    
    // Eole
    'nextCharacterAsleep' => 'obj',
    'abilityActivatedThisTurn' => 'obj',
    'nextAnimalBoost' => 'int',
    'nextAnimalBoostOccurence' => 'int',
    'abilityActivatedThisTurnCount' => 'obj',
    'abilityActivatedThisTurnTypeCount' => 'obj',
    'smokeThemOutArmed' => 'obj', // pId => int (completed support tap charges this assignment turn)
    'samSpookEverDiscarded' => 'obj',

    'newDayManaSelection' => 'obj', // to avoid warning for legacy games
    'testingOption' => 'bool',
    'beginner' => 'int',
    'firstPass' => 'int',

    // Fugue
    'blockedCardNamesThisDay' => 'obj', // pId => card name[]
    'dieRolledThisDay' => 'obj', // pId => bool
  ];

  protected static $table = 'global_variables';
  protected static $primary = 'name';
  protected static function cast($row)
  {
    $val = str_replace('\\\\', '\\\\\\\\', $row['value']);
    $val = json_decode($val, true, 512, JSON_UNESCAPED_SLASHES);

    return self::$variables[$row['name']] == 'int' ? ((int) $val) : $val;
  }

  /*
   * Fetch all existings variables from DB
   */
  protected static $data = [];
  public static function fetch()
  {
    // Turn of LOG to avoid infinite loop (Globals::isLogging() calling itself for fetching)
    $tmp = self::$log;
    self::$log = false;

    foreach (
      self::DB()
        ->select(['value', 'name'])
        ->get(false)
      as $name => $variable
    ) {
      if (\array_key_exists($name, self::$variables)) {
        self::$data[$name] = $variable;
      }
    }
    self::$initialized = true;
    self::$log = $tmp;
  }

  /*
   * Create and store a global variable declared in this file but not present in DB yet
   *  (only happens when adding globals while a game is running)
   */
  public static function create($name)
  {
    if (!\array_key_exists($name, self::$variables)) {
      return;
    }

    $default = [
      'int' => 0,
      'obj' => [],
      'bool' => false,
      'str' => '',
    ];
    $val = $default[self::$variables[$name]];
    self::DB()->insert(
      [
        'name' => $name,
        'value' => \json_encode($val),
      ],
      true
    );
    self::$data[$name] = $val;
  }

  public static function isBreak()
  {
    return !is_null(self::getBreakPlayer()) && self::getBreakPlayer() != -1;
  }

  /*
   * Magic method that intercept not defined static method and do the appropriate stuff
   */
  public static function __callStatic($method, $args)
  {
    if (!self::$initialized) {
      self::fetch();
    }

    if (preg_match('/^([gs]et|inc|is)([A-Z])(.*)$/', $method, $match)) {
      // Sanity check : does the name correspond to a declared variable ?
      $name = mb_strtolower($match[2]) . $match[3];
      if (!\array_key_exists($name, self::$variables)) {
        throw new \InvalidArgumentException("Property {$name} doesn't exist");
      }

      // Create in DB if don't exist yet
      if (!\array_key_exists($name, self::$data)) {
        self::create($name);
      }

      if ($match[1] == 'get') {
        // Basic getters
        return self::$data[$name];
      } elseif ($match[1] == 'is') {
        // Boolean getter
        if (self::$variables[$name] != 'bool') {
          throw new \InvalidArgumentException("Property {$name} is not of type bool");
        }
        return (bool) self::$data[$name];
      } elseif ($match[1] == 'set') {
        // Setters in DB and update cache
        $value = $args[0];
        if (self::$variables[$name] == 'int') {
          $value = (int) $value;
        }
        if (self::$variables[$name] == 'bool') {
          $value = (bool) $value;
        }

        self::$data[$name] = $value;
        self::DB()->update(['value' => \addslashes(\json_encode($value, JSON_UNESCAPED_SLASHES))], $name);
        return $value;
      } elseif ($match[1] == 'inc') {
        if (self::$variables[$name] != 'int') {
          throw new \InvalidArgumentException("Trying to increase {$name} which is not an int");
        }

        $getter = 'get' . $match[2] . $match[3];
        $setter = 'set' . $match[2] . $match[3];
        return self::$setter(self::$getter() + (empty($args) ? 1 : $args[0]));
      }
    }
    throw new \feException(print_r(debug_print_backtrace()));
    return null;
  }

  /*
   * Setup new game
   */
  public static function setupNewGame($players, $options)
  {
    // Storm
    $storm = [];
    $storm[] = ['cardId' => 0, 'visible' => true, 'rotated' => false];
    $stormKeys = [1, 2, 3];
    shuffle($stormKeys);
    for ($i = 0; $i < 3; $i++) {
      $key = array_shift($stormKeys);
      $storm[] = ['cardId' => $key, 'visible' => false, 'rotated' => bga_rand(0, 1) == 1];
    }
    $storm[] = ['cardId' => 4, 'visible' => true, 'rotated' => false];
    self::setStorm($storm);

    self::setDay(0);
    self::setPlayedCards(0);
    self::setDayPhase(false);
    // self::setDeckOptions($options[OPTION_DECKS] ?? 0);
    switch ($options[OPTION_DECK_FORMAT]) {
      case OPTION_DF_FRONTIER:
        self::setDeckFormat('FRONTIER');
        break;
      case OPTION_DF_STANDARD:
        self::setDeckFormat('STANDARD');
        break;
      case OPTION_DF_SINGLETON:
        self::setDeckFormat('SINGLETON_NUC');
        break;
      case OPTION_DF_SANDBOX:
        self::setDeckFormat('SANDBOX');
        break;
      case OPTION_DF_NOUNIQUE:
        self::setDeckFormat('NO_UNIQUE');
        break;  
      case OPTION_DF_TEST:
        self::setDeckFormat('TEST');
        break;
      case OPTION_DF_DEMO:
        self::setDeckFormat('DEMO');
    }
    self::setDeckOptions(OPTION_DECKS_STARTER);
    self::setBeginner($options[OPTION_BEGINNER] ?? 1);
    self::setUndo($options[OPTION_UNDO] ?? 0);
  }

  public static function getStorm($ui = false)
  {
    $storm = self::$data['storm'];

    // Hide hidden datas for UI
    if ($ui) {
      foreach ($storm as $i => &$stormCard) {
        if (!$stormCard['visible']) {
          $stormCard['cardId'] = STORM_BACK;
          unset($stormCard['rotated']);
        }
        unset($stormCard['visible']);
      }
    }

    return $storm;
  }

  public static function getVisibleRegions()
  {
    if (self::isTieBreakerMode()) {
      return ['4' => [FOREST, MOUNTAIN, OCEAN]];
    }

    $stormCards = self::getStorm();
    $storms = [];
    $index = 0;
    foreach ($stormCards as $i => $stormCard) {
      $storm = STORM_CARDS[$stormCard['cardId']];

      if ($stormCard['visible'] == false) {
        $index += 2;
        continue;
      }

      if ($stormCard['rotated']) {
        $storm = array_reverse($storm);
      }

      foreach ($storm as $j => $biomes) {
        if (empty($biomes)) {
          continue;
        }
        $storms[$index] = $biomes;
        $index++;
      }
    }

    return $storms;
  }
}
