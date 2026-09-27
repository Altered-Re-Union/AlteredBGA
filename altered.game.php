<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Altered implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * altered.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */

$swdNamespaceAutoload = function ($class) {
  $classParts = explode('\\', $class);
  if ($classParts[0] == 'ALT') {
    array_shift($classParts);
    $file = dirname(__FILE__) . '/modules/php/' . implode(DIRECTORY_SEPARATOR, $classParts) . '.php';
    if (file_exists($file)) {
      require_once $file;
    } else {
      var_dump(debug_print_backtrace());
      var_dump('Cannot find file : ' . $file);
    }
  }
};
spl_autoload_register($swdNamespaceAutoload, true, true);

require_once APP_GAMEMODULE_PATH . 'module/table/table.game.php';

use ALT\Managers\Meeples;
use ALT\Managers\Cards;
use ALT\Managers\Players;
use ALT\Managers\Scores;
use ALT\Core\Globals;
use ALT\Helpers\Log;
use ALT\Core\Preferences;
use ALT\Core\Stats;
use ALT\Core\Engine;
use ALT\Core\Notifications;

class altered extends Table
{
  use ALT\DebugTrait;
  use ALT\States\SetupTrait;
  use ALT\States\NewDayTrait;
  use ALT\States\EngineTrait;
  use ALT\States\TurnTrait;
  use ALT\States\EndGameTrait;

  public static $instance = null;

  function __construct()
  {
    parent::__construct();
    self::$instance = $this;
    self::initGameStateLabels([
      'logging' => 10,
    ]);
    Engine::boot();
    // Stats::checkExistence();
    // Notifications::resetCache(); // commented as issue as it's needed for Storm calculation

    // EXPERIMENTAL to avoid deadlocks. This locks the global table early in the game constructor.
    $this->bSelectGlobalsForUpdate = true;
  }

  public static function get()
  {
    return self::$instance;
  }

  protected function getGameName()
  {
    return 'altered';
  }

  public function getAllDatas(): array
  {
    return self::localGetAllDatas(false);
  }

  public function localGetAllDatas($refresh = false): array
  {
    $pId = self::getCurrentPId();

    return [
      'prefs' => Preferences::getUiData($pId),
      'players' => Players::getUiData($pId),
      'cards' => Cards::getUiData($pId, $refresh),
      'meeples' => Meeples::getUiData(),
      'undo' => Globals::isUndo(),
      'beginner' => Globals::getBeginner() != 0,
      'deckFormat' => Globals::getDeckFormat(),

      'firstPlayer' => Globals::getFirstPlayer(),
      'passedPlayers' => Globals::isDayPhase() ? Globals::getSkippedPlayers() : [],
      'storm' => Globals::getStorm(true),
      'day' => Globals::getDay(),
      'phase' => Globals::getPhase(),
      'tieBreaker' => Globals::getTieBreakerMode(),

      // Cached infos
      'movements' => Players::computeStorm(),
      'biomes' => Players::getBiomeTotals(),
      'blockedExpeditions' => Players::getBlockedExpeditions(),
      'powersBlockedExpeditions' => Players::getPowersBlockedExpeditions(),
      'defenders' => Players::getDefenders(),
      'reserveSlots' => Players::getReserveSlots(),
      'landmarkSlots' => Players::getLandmarkSlots()
    ];
  }

  function getGameProgression()
  {
    if ($this->getGameName() == 'a' . 'l' . 't' . 'e' . 'r' . 'e' . 'd' . 't' . 'e' . 's' . 't') {
      return 100;
    }

    if (Globals::isTieBreakerMode()) {
      return 99;
    }

    $distance = 99;
    foreach (Players::getAll() as $pId => $player) {
      $d = $player->getRegionDifference();
      $distance = min($distance, $d);
    }

    $progression = (7 - $distance) / 7 * 100;
    if (Globals::getDay() >= 4) {
      return max(50, $progression);
    } else {
      return $progression;
    }
  }

  function actChangePreference($pref, $value)
  {
    Preferences::set($this->getCurrentPId(), $pref, $value);
  }

  ///////////////////////////////////////////////
  ///////////////////////////////////////////////
  ////////////   Custom Turn Order   ////////////
  ///////////////////////////////////////////////
  ///////////////////////////////////////////////
  public function initCustomTurnOrder($key, $order, $callback, $endCallback, $loop = false, $autoNext = true, $args = [])
  {
    $turnOrders = Globals::getCustomTurnOrders();
    $turnOrders[$key] = [
      'order' => $order ?? Players::getTurnOrder(),
      'index' => -1,
      'callback' => $callback,
      'args' => $args, // Useful mostly for auto card listeners
      'endCallback' => $endCallback,
      'loop' => $loop,
    ];
    Globals::setCustomTurnOrders($turnOrders);

    if ($autoNext) {
      $this->nextPlayerCustomOrder($key);
    }
  }

  public function initCustomDefaultTurnOrder($key, $callback, $endCallback, $loop = false, $autoNext = true)
  {
    $this->initCustomTurnOrder($key, null, $callback, $endCallback, $loop, $autoNext);
  }

  public function nextPlayerCustomOrder($key)
  {
    $turnOrders = Globals::getCustomTurnOrders();
    if (!isset($turnOrders[$key])) {
      throw new BgaVisibleSystemException('Asking for the next player of a custom turn order not initialized : ' . $key);
    }

    // Increase index and save
    $o = $turnOrders[$key];
    $i = $o['index'] + 1;
    if ($i == count($o['order']) && $o['loop']) {
      $i = 0;
    }
    $turnOrders[$key]['index'] = $i;
    Globals::setCustomTurnOrders($turnOrders);

    if ($i < count($o['order'])) {
      $this->gamestate->jumpToState(ST_GENERIC_NEXT_PLAYER);
      $this->gamestate->changeActivePlayer($o['order'][$i]);
      $this->jumpToOrCall($o['callback'], $o['args']);
    } else {
      $this->endCustomOrder($key);
    }
  }

  public function endCustomOrder($key)
  {
    $turnOrders = Globals::getCustomTurnOrders();
    if (!isset($turnOrders[$key])) {
      throw new BgaVisibleSystemException('Asking for ending a custom turn order not initialized : ' . $key);
    }

    $o = $turnOrders[$key];
    $turnOrders[$key]['index'] = count($o['order']);
    Globals::setCustomTurnOrders($turnOrders);
    $callback = $o['endCallback'];
    $this->jumpToOrCall($callback);
  }

  public function jumpToOrCall($mixed, $args = [])
  {
    if (is_int($mixed) && array_key_exists($mixed, $this->gamestate->states)) {
      $this->gamestate->jumpToState($mixed);
    } elseif (method_exists($this, $mixed)) {
      $method = $mixed;
      $this->$method($args);
    } else {
      throw new BgaVisibleSystemException('Failing to jumpToOrCall  : ' . $mixed);
    }
  }

  /********************************************
   ******* GENERIC CARD LISTENERS CHECK ********
   ********************************************/
  /*
   * A lot of time you want to loop through all the player to see if a card react or not
   *  => this is achieved using custom turn order with an arg containing the eventType
   *  => the custom order will call the genericPlayerCheckListeners that will getReaction from cards if any
   */
  public function checkCardListeners($typeEvent, $endCallback, $event = [], $order = null)
  {
    $event['type'] = $typeEvent;
    $event['method'] = $typeEvent;
    $event['phase'] = true;
    $this->initCustomTurnOrder($typeEvent, $order, 'genericPlayerCheckListeners', $endCallback, false, true, $event);
  }

  function genericPlayerCheckListeners($event)
  {
    $pId = Players::getActiveId();
    $event['pId'] = $pId;
    $reaction = null;
    $reaction = Cards::getReaction($event);

    // Hard code for Monolith Legate (and such)
    if ($event['type'] == 'BeforeNight') {
      $afterRest = Globals::getAfterRest()[$pId] ?? [];
      if (!empty($afterRest)) {
        // throw new \feException(print_r($afterRest));
        if (is_null($reaction)) {
          $reaction = $afterRest;
        } else {
          $reaction = array_push($reaction, ...$afterRest);
        }
      }
    }
    if ($event['type'] == 'EndOfNight') {
      $afterNight = Globals::getAfterNightCleanup()[$pId] ?? [];
      // throw new \feException(print_r($afterNight));
      if (!empty($afterNight)) {
        // throw new \feException(print_r($afterNight));
        if (is_null($reaction)) {
          $reaction = $afterNight;
        } else {
          $reaction = array_push($reaction, ...$afterNight);
        }
      }
    }

    if (is_null($reaction)) {
      // No reaction => just go to next player
      $this->nextPlayerCustomOrder($event['type']);
    } else {
      // When multiple effects trigger at the same phase, let player choose order
      $reaction = ['type' => NODE_PARALLEL, 'childs' => $reaction, 'noIndependent' => count($reaction) > 1];
      // Reaction => boot up the Engine
      Engine::setup($reaction, ['order' => $event['type']]);
      Engine::proceed();
    }
  }

  //////////////////////////////////////////////////////////////////////////////
  //////////// Zombie
  ////////////

  /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

  function zombieTurn($state, $activePlayer)
  {
    // test to stop inifinte loop
    $nextPlayer = Players::getNext(Players::get($activePlayer));
    $nextPlayer->setScore(1);
    Globals::setZombie(true);
    Stats::setWinner($nextPlayer, 1);
    if (!is_null($nextPlayer->getHero())) {
      Stats::setGameWinner($nextPlayer->getHero()->getStatData());
      Stats::setGameLooser(Players::getNext($nextPlayer)->getHero()->getStatData());
    }
    $this->gamestate->jumpToState(ST_PRE_END_OF_GAME);
    return;

    $stateName = $state['name'];
    if ($state['type'] == 'activeplayer') {
      if ($stateName == 'confirmTurn') {
        $this->actConfirmTurn(true);
      } elseif ($stateName == 'confirmPartialTurn') {
        $this->actConfirmPartialTurn(true);
      }
      // Clear all node of player
      elseif (Engine::getNextUnresolved() != null) {
        Engine::clearZombieNodes($activePlayer);
        Engine::proceed();
      } else {
        $this->gamestate->nextState('zombiePass');
      }
    } elseif ($state['type'] == 'multipleactiveplayer') {
      if ($stateName == 'selectPrecoDeck') {
        $selection = Globals::getDeckSelection();
        $selection[$activePlayer] = 0;
        Globals::setDeckSelection($selection);
        $this->updateActivePlayersPrecoDeckSelection();
      } else if ($stateName == 'firstDayManaSelection') {
        $args = $this->argsFirstDayManaSelection()['_private'][$activePlayer];
        $cardIds = [$args['cards'][0], $args['cards'][1], $args['cards'][2]];

        $selection = Globals::getFirstDayManaSelection();
        $selection[$activePlayer] = $cardIds;
        Globals::setFirstDayManaSelection($selection);
        $this->updateActivePlayersFirstDayManaSelection();
      }
      // Make sure player is in a non blocking status for role turn
      else {
        $this->gamestate->setPlayerNonMultiactive($activePlayer, 'zombiePass');
      }
    }
  }

  function onConcede($concederTeam)
  {
    $loser = $concederTeam->players[0]->id;
    // test to stop inifinte loop
    $nextPlayer = Players::getNext(Players::get($loser));
    $nextPlayer->setScore(1);
    Stats::setWinner($nextPlayer, 1);
    if (!is_null($nextPlayer->getHero())) {
      Stats::setGameWinner($nextPlayer->getHero()->getStatData());
      Stats::setGameLooser(Players::getNext($nextPlayer)->getHero()->getStatData());
    }
    $this->stPreEndOfGame(true);
  }

  ///////////////////////////////////////////////////////////////////////////////////:
  ////////// DB upgrade
  //////////

  /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

  function upgradeTableDb($from_version) {}

  /////////////////////////////////////////////////////////////
  // Exposing protected methods, please use at your own risk //
  /////////////////////////////////////////////////////////////

  // Exposing protected method getCurrentPlayerId
  public static function getCurrentPId()
  {
    return self::get()->getCurrentPlayerId();
  }

  // Exposing protected method translation
  public static function translate($text)
  {
    return self::get()->_($text);
  }

  public function localCheckAction($actionName, $bThrowException = true)
  {
    $doable = self::checkAction($actionName, false);
    if (!$doable && $bThrowException) {
      throw new feException(_('This game action is impossible right now') . $actionName, true, true, FEX_game_action_no_allowed);
    }
  }
}
