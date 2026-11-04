<?php

namespace ALT;

use ALT\Core\Globals;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Engine;
use ALT\Core\Game;
use ALT\Core\Notifications;
use ALT\Helpers\Utils;
use ALT\Helpers\FT;
use ALT\Core\Stats;
use ALT\Helpers\Collection;
use ALT\Helpers\FlowConvertor;
use ALT\Managers\Meeples;

trait DebugTrait
{
  function actDisplayAllCards()
  {
    $datas = [];

    require_once dirname(__FILE__) . '/Cards/cards.inc.php';
    // foreach (DEMO as $faction => $deck) {
    //   foreach ($deck as $cardId => $n) {
    //     $className = '\\ALT\\Cards\\' . $faction . '\\' . $cardId;
    //     $class = new $className(null);
    //     $datas[] = $class->jsonSerialize();
    //   }
    // }

    foreach (MAP_REFS_CLASSES as $cardId => $className) {
      $className = '\\ALT\\Cards\\' . str_replace('/', '\\', $className);
      $class = new $className(null);
      $datas[] = $class->jsonSerialize();
    }

    // require_once dirname(__FILE__) . '/../../misc/list.inc.php';
    // foreach (ALL_CARDS as $filename) {
    //   if (!file_exists(dirname(__FILE__) . '/../../misc/CoreSetV3/' . $filename . '.php')) {
    //     continue;
    //   }
    //   require_once dirname(__FILE__) . '/../../misc/CoreSetV3/' . $filename . '.php';
    //   $t = explode('/', $filename);
    //   $className = '\\ALT\\Cards\\' . $t[0] . '\\' . $t[1];
    //   $class = new $className(null);

    //   if ($class->getRarity() == RARITY_COMMON) {
    //     $datas[] = $class;
    //   }
    // }

    return $datas;
  }

  function decks()
  {
    // $json = toto(dirname(__FILE__) . '/../../misc/prod_assets.json');
    // $data = json_decode($json, true);
    // // throw new \feException(print_r($data));
    // $cardList = '';
    // foreach ($data['hydra:member'] as $card) {
    //   $cardList .= "'" . $card['reference'] . "',";
    // }

    // throw new \feException($cardList);
    // foreach (ALL_CARDS as $filename) {
    //   if (!file_exists(dirname(__FILE__) . '/../../misc/CoreSetV3/' . $filename . '.php')) {
    //     continue;
    //   }
    //   require_once dirname(__FILE__) . '/../../misc/CoreSetV3/' . $filename . '.php';
    //   $t = explode('/', $filename);
    //   $className = '\\ALT\\Cards\\' . $t[0] . '\\' . $t[1];
    //   $class = new $className(null);

    //   if ($class->getRarity() == RARITY_COMMON) {
    //     $datas[] = $class;
    //   }
    // }
  }

  function tp()
  {
    $markers = Meeples::createHeroMarkers();
    if (!empty($markers)) {
      Notifications::addTerrainMarkers($markers);
    }
  }

  function dv()
  {
    Globals::setEndTriggered(true);
  }

  function debug_deck($deckNumber)
  {
    $deckContent = $this->actGetDeckInfos($deckNumber);
    $deckContent['cards'][HERO]['card'] = $deckContent['cards'][HERO]['card']->jsonSerialize();
    $this->actConfirmAPIDeck($deckContent);
  }

  function vt()
  {
    // throw new \feException(print_r(Engine::getNextUnresolved()->getParent()->toArray()));
    // Meeples::createHeroMarkers();
    $turnCards = Globals::getTurnCards();
    $turnCards[Players::getCurrent()->getId()] = ($turnCards[Players::getCurrent()->getId()] ?? 0) + 1;
    Globals::setTurnCards($turnCards);
    // throw new \feException(print_r(Players::getCurrent()->getStormToken()));
    // throw new \feException(Players::getCurrent()->isAscended(COMPANION));
    // Cards::getCardClass('ALT_BISE_P_BR_64_R2');
    // throw new \BgaUserException(clienttranslate(sprintf(self::_("The card %s is temporarily suspended by Equinox"), 'toto')));
    // throw new \feException(print_r(Engine::getNextUnresolved()->toArray()));
    // Globals::setupNewGame([], []);
    // Cards::setupNewGame(Players::getAll()->getIds(), []);
    // $this->actFirstDayMana([17, 21, 22]);
    // $this->actDayMana([20]);

    // Cards::get(3)->boost(1, 'test', true);
    // throw new \feException(print_r(Players::getCurrent()->isInBiome(STORM_LEFT, FOREST)));
    // $this->actTakeAtomicAction('actMoveRegionMarker', [8]);
    // $this->actTakeAtomicAction('actReserve', [29, STORM_LEFT]);
    // $this->actTakeAtomicAction('actSupport', [226]);
    // $this->actTakeAtomicAction('actTap', [236]);
    // $this->actTakeAtomicAction('actPass', []);
    // $this->actTakeAtomicAction('actDiscard', [[9]]);
    // $this->actTakeAtomicAction('actTarget', [[17]]);
    // $this->actTakeAtomicAction('actDiscardAdd', [577]);
    // $BGAToken = self::masterNodeRequest('getGameSpecificMetaInfos', [
    //   'game' => 'alter' . 'ed',
    //   'mode' => 'cards',
    //   'cardsid' => ["ALT_COREKS_B_LY_04_U_4874", "ALT_COREKS_B_MU_12_U_1367"]
    // ]);
    // $BGAToken = $this->equinoxAPIConnect(['mode' => 'BGALogin']);
    // var_dump($BGAToken);
    // ['token'];
    // throw new \feException(Cards::get(11)->countToken(FLEETING));
    // Stats::incDays(2);
    // Stats::setWinner(Players::getActive(), true);
    // throw new \feException(print_r(Globals::getDeckContent())); //->isDefender());
  }

  function tv($a)
  {
    // Cards::get(24)->setTapped(true);
    // Cards::get(3)->setEffectHand([[TARGET_ALL_CHARACTER_2 => [[BOOST => 2], [BOOST => 2]]]]);
    // Cards::get(32)->setEffectPassive([
    //   'ChooseAssignment' => [
    //     'condition' => 'firstCharacterPlayed',
    //     'output' => FT::SEQ(FT::GAIN(EFFECT, BOOST), ['action' => SPECIAL_EFFECT, 'args' => ['effect' => 'useCard']]),
    //   ],
    // ]);
    // Cards::get(1)->isListeningTo([]);
    // throw new \feException(print_r(Players::get(2305528)->getBiomeInStorms()));
    // Notifications::updateTotalMana();
    // $card = Cards::getCardClass('ALT_COREKS_B_YZ_17_R1');
    // // throw new \feException(print_r($card->getProperties()));
    // // throw new \feException("toto");
    // $player = Players::getCurrent();

    // Cards::singleCreate([
    //   'player_id' => $player->getId(),
    //   'location' => HAND,
    //   'nbr' => 1,
    //   'properties' => $card->getProperties(),
    // ]);
    // Notifications::refreshUI($this::get()->getAllDatas(true));
    // $player = Players::getCurrent();
    // Notifications::refreshHand($player, $player->getHand()->ui(), $player->getManaCards()->ui());
    // Engine::proceed();
    // Players::getActive()->isInBiome(STORM_RIGHT, OCEAN);
    $card = Cards::getCardClass(trim($a))->jsonSerialize();

    Cards::singleCreate([
      'player_id' => Players::getCurrentId(),
      'location' => 'hand',
      'nbr' => 1,
      'properties' => $card['properties'],
    ]);
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
    $player = Players::getCurrent();
    Notifications::refreshHand($player, $player->getHand()->ui(), $player->getManaCards()->ui());
    Engine::proceed();

    // throw new \feException(print_r(Cards::getCardClass(trim($a))->jsonSerialize()));
  }

  /**
   * Force the game into the arena (tie-breaker) mode.
   *
   * Mirrors the real entry performed when both players cross their tokens on the same
   * phase (see Players::checkVictory): both tokens are moved onto the final regions,
   * every region marker is removed and tie-breaker mode is enabled. Useful to reproduce
   * arena-only behaviours such as Will-o'-the-Wisp on a gigantic character.
   */
  function tiebreak()
  {
    Globals::setTieBreakerMode(true);
    $meeples = new Collection();
    foreach (Players::getAll() as $pId => $player) {
      $player->getCompanionToken()->setLocation('storm-4');
      $player->getHeroToken()->setLocation('storm-3');
      $meeples = $meeples->merge(Meeples::getStormTokens($pId));
    }

    // Delete/remove markers
    $markers = Meeples::getOfType('storm-3', [OCEAN, FOREST, MOUNTAIN])->merge(Meeples::getOfType('storm-4', [OCEAN, FOREST, MOUNTAIN]));
    foreach ($markers as $mId => &$marker) {
      Meeples::delete($marker->getId());
    }

    // notif startTiebreak
    Notifications::startTiebreak($meeples->toArray());
    Notifications::silentKill($markers->getIds());
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
  }

  function resolveDebug()
  {
    Engine::resolveAction([]);
    Engine::proceed();
  }
  
  /**
   * Studio helpers for empty / partial deck testing.
   * Chat examples: debug_emptyDeck(), debug_emptyDeck('opponent'), debug_setDeckCount(3), debug_deckInfo()
   */
  private function getPlayers(string $who = 'me')
  {
    if ($who === 'both') {
      return Players::getAll();
    }
    $player =
      $who === 'opponent' || $who === 'opp'
        ? Players::getNext(Players::getCurrent())
        : Players::getCurrent();

    return new Collection([$player->getId() => $player]);
  }

  // add "debug_" before to see the function in debug tools
  function emptyDeck(string $who = 'me')
  {
    foreach ($this->getPlayers($who) as $pId => $player) {
      Cards::moveAllInLocation("deck-$pId", DISCARD_PILE);
      Cards::moveAllInLocation("reveal-$pId", DISCARD_PILE);
    }
    $this->updateDeckInfo(true);
  }

  // add "debug_" before to see the function in debug tools
  function setDeckCount(int $n, string $who = 'me')
  {
    $n = max(0, $n);
    foreach ($this->getPlayers($who) as $pId => $player) {
      Cards::moveAllInLocation("reveal-$pId", DISCARD_PILE);
      $deckLoc = "deck-$pId";
      $count = Cards::countInLocation($deckLoc);
      if ($count > $n) {
        Cards::pickForLocation($count - $n, $deckLoc, DISCARD_PILE);
      } elseif ($count < $n) {
        $missing = $n - $count;
        $inDiscard = Cards::getFiltered($pId, DISCARD_PILE)->orderBy('state', 'DESC')->limit($missing);
        foreach ($inDiscard as $cId => $card) {
          Cards::insertOnTop($cId, $deckLoc);
        }
      }
    }
    $this->updateDeckInfo();
  }

  private function updateDeckInfo(bool $refreshUi = false)
  {
    $lines = [];
    foreach (Players::getAll() as $pId => $player) {
      $deck = Cards::countInLocation("deck-$pId");
      $reveal = Cards::countInLocation("reveal-$pId");
      $discard = Cards::getFiltered($pId, DISCARD_PILE)->count();
      $lines[] =
        $player->name .
        " (#$pId): deck=$deck reveal=$reveal discard=$discard hasDeck=" .
        ($player->hasDeckCards() ? 'yes' : 'no');
    }
    self::notifyAllPlayers('message', implode(' — ', $lines), []);
    if ($refreshUi) {
      Notifications::refreshUI($this::get()->localGetAllDatas(true));
      Engine::proceed();
    }
  }

  function tapAllMana()
  {
    foreach (Cards::getAll() as $cId => $card) {
      if ($card->getLocation() == MANA) {
        $card->setTapped(true);
      }
    }
  }

  function tapLandmarks()
  {
    foreach (Cards::getAll() as $cId => $card) {
      if ($card->getLocation() == LANDMARK) {
        $card->setTapped(true);
      }
    }
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
  }

  function untapAll()
  {
    Cards::untapAll();
  }

  function tapReserve()
  {
    $player = Players::getCurrent();
    foreach ($player->getReserveCards() as $cId => $card) {
      $card->setTapped(true);
    }
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
  }

  function tapMana()
  {
    $player = Players::getCurrent();
    foreach ($player->getManaCards() as $cId => $card) {
      $card->setTapped(true);
    }
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
  }

  /**
   * Force the counter count on the player hero (if any) to a specified value.
   *
   * Used to debug triggers on Sol (Halua summon), Treyst or Atsadi
   */
  function setHeroCounters(int $n = 5)
  {
    $hero = Players::getCurrent()->getHero();
    if (is_null($hero)) {
      throw new \feException('No hero found');
    }
    $data = $hero->getExtraDatas();
    $data['counter'] = $n;
    $data['counterName'] = $data['counterName'] ?? clienttranslate('Quest counters');
    $hero->setExtraDatas($data);
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
  }

  function allVisible()
  {
    $sql = "UPDATE `cards` set `card_state` = 1 where `card_location` like 'turn%'";
    self::DbQuery($sql);
  }

  function playCardAux($cardId, $doAction = true)
  {
    $player = Players::getCurrent();
    $pId = $player->getId();

    $sql = "SELECT * FROM cards WHERE card_id = '$cardId' LIMIT 1";
    $card = self::getUniqueValueFromDB($sql);

    if (is_null($card)) {
      $sql = "UPDATE cards set card_id = '$cardId' where player_id = $pId AND `card_location` <> 'inPlay' LIMIT 1";
    } else {
      $sql = "UPDATE cards set player_id = $pId where card_id = '$cardId'";
    }
    self::DbQuery($sql);

    if ($doAction) {
      $this->actTakeAtomicAction([$cardId]);
    }
  }

  function addHand($cardId)
  {
    $player = Players::getCurrent();
    $pId = $player->getId();
    $sql = "UPDATE cards set player_id = $pId, card_location = 'hand' where card_id = '$cardId'";
    self::DbQuery($sql);
    Notifications::drawCards($player, new Collection([Cards::get($cardId)]));
    // $this->insertAsChild([
    //   'action' => CHOOSE_ACTION_CARD,
    //   'pId' => $player->getId(),
    // ]);
    // Engine::resolveAction();
    Engine::proceed();
  }

  function playCard($cardId)
  {
    $this->playCardAux($cardId, true);
  }

  function addAlt($uId, $location = 'hand')
  {
    $card = Cards::getCardClass($uId);
    $player = Players::getCurrent();
    Cards::singleCreate([
      'player_id' => $player->getId(),
      'location' => $location,
      'nbr' => 1,
      'properties' => $card->getProperties(),
    ]);
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
    $player = Players::getCurrent();
    Notifications::refreshHand($player, $player->getHand()->ui(), $player->getManaCards()->ui());
    Engine::proceed();
  }

  // cardId is something like BR_Common_RekaFisherman, and location can be hand, reserve, stormLeft, stormRight. 
  // see all possible location in constants.inc.php
  function debug_addcard(string $cardId, string $location = 'hand')
  {
    $this->addCard($cardId, $location);
  }

  function addCard($cardId, $location = 'hand')
  {
    $player = Players::getCurrent();
    $faction = substr($cardId, 0, 2);
    $className = "\\ALT\\Cards\\$faction\\$cardId";
    $card = new $className(null);

    Cards::singleCreate([
      'player_id' => $player->getId(),
      'location' => $location,
      'nbr' => 1,
      'properties' => $card->getProperties(),
    ]);
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
    $player = Players::getCurrent();
    Notifications::refreshHand($player, $player->getHand()->ui(), $player->getManaCards()->ui());
    Engine::proceed();
  }

  function drawCard($cardId)
  {
    $this->playCardAux($cardId, false);
    $sql = "UPDATE cards set card_location = 'hand' where card_id = '$cardId'";
    self::DbQuery($sql);
  }

  function engDisplay()
  {
    throw new \feException(print_r(Globals::getEngine()));
  }

  function engProceed()
  {
    Engine::proceed();
  }

  /*
   * loadBug: in studio, type loadBug(20762) into the table chat to load a bug report from production
   * client side JavaScript will fetch each URL below in sequence, then refresh the page
   */
  public function loadBug($reportId)
  {
    die("Obsolete");
    $db = explode('_', self::getUniqueValueFromDB("SELECT SUBSTRING_INDEX(DATABASE(), '_', -2)"));
    $game = $db[0];
    $tableId = $db[1];
    self::notifyAllPlayers(
      'loadBug',
      "Trying to load <a href='https://boardgamearena.com/bug?id=$reportId' target='_blank'>bug report $reportId</a>",
      [
        'urls' => [
          // Emulates "load bug report" in control panel
          "https://studio.boardgamearena.com/admin/studio/getSavedGameStateFromProduction.html?game=$game&report_id=$reportId&table_id=$tableId",

          // Emulates "load 1" at this table
          "https://studio.boardgamearena.com/table/table/loadSaveState.html?table=$tableId&state=1",

          // Calls the function below to update SQL
          "https://studio.boardgamearena.com/1/$game/$game/loadBugSQL.html?table=$tableId&report_id=$reportId",

          // Emulates "clear PHP cache" in control panel
          // Needed at the end because BGA is caching player info
          "https://studio.boardgamearena.com/admin/studio/clearGameserverPhpCache.html?game=$game",
        ],
      ]
    );
  }

  /*
   * loadBugSQL: in studio, this is one of the URLs triggered by loadBug() above
   */
  public function loadBugReportSQL($reportId, $studioPlayersIds)
  {
    $players = self::getObjectListFromDb('SELECT player_id FROM player', true);

    // Change for your game
    // We are setting the current state to match the start of a player's turn if it's already game over
    $sql = ['UPDATE global SET global_value=2 WHERE global_id=1 AND global_value=99'];
    $sql[] = 'ALTER TABLE `gamelog` ADD `cancel` TINYINT(1) NOT NULL DEFAULT 0;';
    $map = [];
    foreach ($players as $index => $pId) {
      $studioPlayer = $studioPlayersIds[$index];
      $map[(int) $pId] = (int) $studioPlayer;

      // All games can keep this SQL
      $sql[] = "UPDATE player SET player_id=$studioPlayer WHERE player_id=$pId";
      $sql[] = "UPDATE global SET global_value=$studioPlayer WHERE global_value=$pId";
      $sql[] = "UPDATE stats SET stats_player_id=$studioPlayer WHERE stats_player_id=$pId";

      // Add game-specific SQL update the tables for your game
      $sql[] = "UPDATE meeples SET player_id=$studioPlayer WHERE player_id=$pId";
      $sql[] = "UPDATE cards SET player_id=$studioPlayer WHERE player_id=$pId";
      $sql[] = "UPDATE cards SET card_location='deck-$studioPlayer' WHERE card_location='deck-$pId'";
      $sql[] = "UPDATE cards SET card_location='board-hero-$studioPlayer' WHERE card_location='board-hero-$pId'";
      $sql[] = "UPDATE user_preferences SET player_id=$studioPlayer WHERE player_id=$pId";
    }
    $msg =
      "<b>Loaded <a href='https://boardgamearena.com/bug?id=$reportId' target='_blank'>bug report $reportId</a></b><hr><ul><li>" .
      implode(';</li><li>', $sql) .
      ';</li></ul>';
    self::warn($msg);
    self::notifyAllPlayers('message', $msg, []);

    foreach ($sql as $q) {
      self::DbQuery($q);
    }

    /******************
     *** Fix Globals ***
     ******************/

    // Turn orders
    $turnOrders = Globals::getCustomTurnOrders();
    foreach ($turnOrders as $key => &$order) {
      $t = [];
      foreach ($order['order'] as $pId) {
        $t[] = $map[$pId];
      }
      $order['order'] = $t;
    }
    Globals::setCustomTurnOrders($turnOrders);

    // Engine
    $engine = Globals::getEngine();
    self::loadDebugUpdateEngine($engine, $map);
    Globals::setEngine($engine);

    // First player
    $fp = Globals::getFirstPlayer();
    Globals::setFirstPlayer($map[$fp]);

    // Active player
    $ap = Globals::getActivePId();
    if ($ap != 0) {
      Globals::setActivePId($map[$ap]);
    }

    // firstDayManaSelection
    $t = Globals::getDeckSelection();
    $u = [];
    foreach ($t as $pId => $choice) {
      $u[$map[$pId]] = $choice;
    }
    Globals::setDeckSelection($u);

    // firstDayManaSelection
    $t = Globals::getFirstDayManaSelection();
    $u = [];
    foreach ($t as $pId => $choice) {
      $u[$map[$pId]] = $choice;
    }
    Globals::setFirstDayManaSelection($u);

    // deckContent
    $t = Globals::getDeckContent();
    $u = [];
    foreach ($t as $pId => $choice) {
      $u[$map[$pId]] = $choice;
    }
    Globals::setDeckContent($u);


    // firstDayManaSelection
    $t = Globals::getPlayerDecks();
    $u = [];
    foreach ($t as $pId => $choice) {
      $u[$map[$pId]] = $choice;
    }
    Globals::setPlayerDecks($u);

    // skippedPlayers
    $t = Globals::getSkippedPlayers();
    $u = [];
    foreach ($t as $pId) {
      $u[] = $map[$pId];
    }
    Globals::setSkippedPlayers($u);

    self::reloadPlayersBasicInfos();
  }

  static function walkReplacePId(&$t, $map)
  {
    if (isset($t['pId'])) {
      $t['pId'] = $map[(int) $t['pId']] ?? $t['pId'];
    }
    if (isset($t['pIds'])) {
      foreach ($t['pIds'] as &$pId) {
        $pId = $map[(int) $pId] ?? $pId;
      }
    }

    foreach ($t as $key => &$v) {
      if (is_array($v)) {
        self::walkReplacePId($v, $map);
      }
    }
  }

  static function loadDebugUpdateEngine(&$node, $map)
  {
    if (isset($node['pId'])) {
      $node['pId'] = $map[(int) $node['pId']];
    }
    if (isset($node['args'])) {
      self::walkReplacePId($node['args'], $map);
    }

    if (isset($node['childs'])) {
      foreach ($node['childs'] as &$child) {
        self::loadDebugUpdateEngine($child, $map);
      }
    }
  }

  function testZomb()
  {
    $this->zombieTurn([], 2305527);
  }



  ///////////////////////////////////////////
  //// API : ONLY FOR UNIQUES ON STUDIO
  ///////////////////////////////////////////

  function equinoxAPIConnect($params)
  {
    $mode = $params['mode'];
    $user = $params['user'] ?? '';
    $secret = $params['secret'] ?? '';
    $token = $params['token'] ?? '';
    $deckNumber = $params['deckNumber'] ?? '';
    $cardId = $params['cardId'] ?? '';
    //$curl = curl_VTOinit();
    // $baseUrl = 'https://api.equinox-ccg.io';
    $baseUrl = 'https://api.altered.gg';
    $setup = [
      CURLOPT_URL => $baseUrl . '/login',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ];

    switch ($mode) {
      case 'login':
        $setup[CURLOPT_URL] = $baseUrl . '/login';
        $setup[CURLOPT_POSTFIELDS] =
          '{
              "email": "' .
          $user .
          '",
              "password": "' .
          $secret .
          '"
          }';
        $setup[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
        $setup[CURLOPT_CUSTOMREQUEST] = 'POST';
        break;
      case 'BGALogin':
        $setup[CURLOPT_URL] = $baseUrl . '/login';
        $setup[CURLOPT_POSTFIELDS] =
          '{
          "email": "' .
          'bga@equinox.fr' .
          '",
          "password": "' . 'Q39jXhb7E6HnZEbc' .
          '"
      }';
        $setup[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
        $setup[CURLOPT_CUSTOMREQUEST] = 'POST';
        break;
      case 'deckList':
        // token of the player
        $setup[CURLOPT_URL] = $baseUrl . '/deck_user_lists/?isLegal=true';
        $setup[CURLOPT_HTTPHEADER] = ['token: ' . $token, 'Authorization: Bearer ' . $token];
        $setup[CURLOPT_CUSTOMREQUEST] = 'GET';
        // throw new \feException(print_r($setup));
        break;
      case 'deck':
        // token of the player
        $setup[CURLOPT_URL] = $baseUrl . $deckNumber;
        $setup[CURLOPT_HTTPHEADER] = ['token: ' . $token, 'Authorization: Bearer ' . $token];
        $setup[CURLOPT_CUSTOMREQUEST] = 'GET';
        break;
      case 'card':
        // BGA token
        $setup[CURLOPT_URL] = $baseUrl . '/cards/' . $cardId;
        $setup[CURLOPT_HTTPHEADER] = ['token: ' . $token, 'Authorization: Bearer ' . $token];
        $setup[CURLOPT_CUSTOMREQUEST] = 'GET';
        break;
    }
    curl_setopt_array($curl, $setup);
    $response = json_decode(curl_exec($curl), true);
    curl_close($curl);
    return $response;
  }

  // change this function content if you need a specific setup requiring more than 1 card to test a scenario, 
  // like adding specific cards on the board or in hand
  function debug_setup()
  {
    // Cards that invoke the above tokens when played from hand.
    $this->addCard('YZ_Common_MothDecoy', 'hand'); // -> ManaMoth
    $this->addCard('MU_Common_CountingSheep', 'hand'); // -> Woollyback x2
    $this->addCard('AX_Common_BugOutBag', 'hand'); // -> Brassbug
    $this->addCard('OD_Rare_SunisaOrdisBodyguard', 'hand'); // -> OrdisRecruit
  }
  
  // Function used to forcibly clean up an entire location (deck, hand, reserve or both).
  // Sends all cards in given location to discard pile, and refreshes UI. Useful for testing specific scenarios.
  // function debug_emptyTargetLocation(string $location = HAND)
  // {
  //   $player = Players::getCurrent();
  //   $pId = $player->getId();
  //   $deckLocation = "deck-$pId";
  //   $revealLocation = "reveal-$pId";

  //   switch (strtoupper($location)) {
  //     case 'HAND':
  //       $locations = [HAND];
  //       break;
  //     case 'RESERVE':
  //       $locations = [RESERVE];
  //       break;
  //     case 'DECK':
  //       $locations = [$deckLocation, $revealLocation];
  //       break;
  //     case 'ALL':
  //       $locations = [HAND, RESERVE, $deckLocation, $revealLocation];
  //       break;
  //     default:
  //       throw new \feException("Invalid location '$location' (expected HAND, RESERVE, DECK or ALL)");
  //   }

  //   foreach ($locations as $loc) {
  //     foreach (Cards::getFiltered($pId, $loc) as $card) {
  //       $card->discard();
  //     }
  //   }

  //   Notifications::refreshUI($this::get()->localGetAllDatas(true));
  //   Notifications::refreshHand($player, $player->getHand()->ui(), $player->getManaCards()->ui());
  //   Engine::proceed();
  // }

  /**
   * Load a unique card with one or more effect trigrams.
   *
   * Spec format: "trigger/condition/output" groups separated by ";".
   * For on-play effects, use zone letters as trigger: H (hand), R (reserve), J or P (played).
   * For passive effects, use the numeric trigger id (e.g. 17 for At Dusk).
   *
   * Examples:
   *   H/166/29;R/167/30;J/168/31
   *   22/166/29;1/167/30;24/168/31
   *   17/166/29;H/882/833
   */
  function debug_loadUniqueTrigrams(string $spec, string $location = HAND)
  {
    $this->loadUniqueFromTrigrams($spec, $location);
  }

  private function loadUniqueFromTrigrams(string $spec, string $location = HAND)
  {
    $trigrams = [];
    foreach (explode(';', $spec) as $part) {
      $part = trim($part);
      if ($part === '') {
        continue;
      }
      $pieces = preg_split('#/+#', $part);
      if (count($pieces) !== 3) {
        throw new \feException("Invalid trigram '$part' (expected trigger/condition/output, groups separated by ';')");
      }
      $trigrams[] = [
        'trigger' => $pieces[0],
        'condition' => $pieces[1],
        'output' => $pieces[2],
      ];
    }
    if (empty($trigrams)) {
      throw new \feException('No trigrams provided');
    }

    $player = Players::getCurrent();
    $faction = $player->getFaction() ?: FACTION_BR;
    $properties = Cards::generateUniqueFromTrigrams($faction, $trigrams);

    Cards::singleCreate([
      'player_id' => $player->getId(),
      'location' => $location,
      'nbr' => 1,
      'properties' => $properties,
    ]);
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
    Notifications::refreshHand($player, $player->getHand()->ui(), $player->getManaCards()->ui());
    Engine::proceed();
  }

  function debug_loadUnique(string $v, string $location = HAND)
  {
    $this->loadUnique($v, $location);
  }

  function loadUnique($v = null, $location = HAND)
  {
    // $token = $this->equinoxAPIConnect(['mode' => 'BGALogin'])['token'];
    // $unique = $this->equinoxAPIConnect(['mode' => 'card', 'token' => $token, 'cardId' => $v]);

    // // throw new \feException(print_r($unique));

    // $uniqueReduced = [];
    // $uniqueReduced['reference'] = $unique['reference'];
    // $uniqueReduced['faction'] = $unique['mainFaction']['reference'];
    // $uniqueReduced['name'] = $unique['name'];
    // $uniqueReduced['cardType'] = $unique['cardType']['reference'];
    // $subtypes = [];
    // $typeline = ['Character'];
    // foreach ($unique['cardSubTypes'] ?? [] as $v => $sub) {
    //   // ?? [] is temp!
    //   $subtypes[] = $sub['reference'];
    //   $typeline[] = $sub['name'];
    // }
    // $uniqueReduced['subTypes'] = $subtypes;
    // $uniqueReduced['typeline'] = $typeline;
    // $uniqueReduced['illustrator'] =  $unique['illustrator']['nickName'];
    // $uniqueReduced['costHand'] = (int) $unique['elements']['MAIN_COST'];
    // $uniqueReduced['costReserve'] = (int) $unique['elements']['RECALL_COST'];
    // $uniqueReduced['forest'] = (int) $unique['elements']['FOREST_POWER'];
    // $uniqueReduced['mountain'] = (int) $unique['elements']['MOUNTAIN_POWER'];
    // $uniqueReduced['ocean'] = (int) $unique['elements']['OCEAN_POWER'];

    // foreach ($unique['cardElements'] as $i => $cardElement) {
    //   if (
    //     $cardElement['cardElementType']['reference'] != 'MAIN_EFFECT' &&
    //     $cardElement['cardElementType']['reference'] != 'ECHO_EFFECT'
    //   ) {
    //     continue;
    //   }
    //   foreach ($cardElement['cardEffectDisplays'] as $i2 => $effect) {
    //     $trinity = [];
    //     foreach ($effect['cardEffect']['cardEffectElements'] as $i3 => $indivEffect) {
    //       $trinity[] =  $indivEffect['idGd'];
    //     }
    //     if (empty($trinity)) {
    //       continue;
    //     }
    //     if (count($trinity) != 3) {
    //       continue;
    //     }
    //     $uniqueReduced['uniqueReduced'][0]['effects'][] = $trinity;
    //   }
    // }

    $uniqueReduced = self::getGenericGameInfos('get_unique_card_definition', ['card_id' => $v]);

    // throw new \feException(print_r($uniqueReduced));
    $properties = Cards::generateUnique($uniqueReduced['content']);
    // throw new \feException(print_r($properties));

    Cards::singleCreate([
      'player_id' => Players::getCurrentId(),
      'location' => $location,
      'nbr' => 1,
      'properties' => $properties,
    ]);
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
    $player = Players::getCurrent();
    Notifications::refreshHand($player, $player->getHand()->ui(), $player->getManaCards()->ui());
    Engine::proceed();
  }
  function testUnique($effect, $location = HAND)
  {
    $ceg = explode("_", $effect);
    if (count($ceg) == 1) {
      $ceg = explode(' / ', $effect);
    }
    if (count($ceg) == 1) {
      $ceg = explode('/', $effect);
    }
    $uniqueCard = [
      'reference' => 'ALT_ALIZE_B_MU_33_U',
      'faction' => 'MU',
      'name' => 'Fake unique for testing',
      'cardType' => 'CHARACTER',
      'illustrator' => 'TOTO',
      'costHand' => 2,
      'costReserve' => 2,
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'uniqueReduced' => [
        [
          'effects' => [
            [
              $ceg[0],
              $ceg[1],
              $ceg[2]
            ]
          ]
        ]
      ]
    ];

    $properties = Cards::generateUnique($uniqueCard);
    Cards::singleCreate([
      'player_id' => Players::getCurrentId(),
      'location' => $location,
      'nbr' => 1,
      'properties' => $properties,
    ]);
    Notifications::refreshUI($this::get()->localGetAllDatas(true));
    $player = Players::getCurrent();
    Notifications::refreshHand($player, $player->getHand()->ui(), $player->getManaCards()->ui());
    Engine::proceed();
  }
}
