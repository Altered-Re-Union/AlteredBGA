<?php

namespace ALT\States;

use ALT\Core\Globals;
use ALT\Core\Notifications;
use ALT\Core\Engine;
use ALT\Core\Stats;
use ALT\Core\Preferences;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Managers\Meeples;
use ALT\Helpers\Log;

trait SetupTrait
{
  /*
   * setupNewGame:
   */
  protected function setupNewGame($players, $options = [])
  {
    Globals::setupNewGame($players, $options);
    Players::setupNewGame($players, $options);
    // Preferences::setupNewGame($players, $this->player_preferences);

    Globals::setFirstPlayer($this->getNextPlayerTable()[0]);
    Players::initializeDecks();
    Stats::checkExistence();

    $this->setGameStateInitialValue('logging', false);
    $this->gamestate->setAllPlayersMultiactive();
    $this->activeNextPlayer();
  }


  //////////////////////////////////////////////////////////////////
  //  ____
  // |  _ \ _ __ ___  ___ ___  ___
  // | |_) | '__/ _ \/ __/ _ \/ __|
  // |  __/| | |  __/ (_| (_) \__ \
  // |_|   |_|  \___|\___\___/|___/
  //////////////////////////////////////////////////////////////////

  function getDeckHero($deckNumber)
  {
    return Cards::getInLocation("deck-$deckNumber")
      ->where('type', HERO)
      ->first();
  }
  
  function getDemoDeckPreview($pId, $deckNumber)
  {
    require_once dirname(__FILE__) . '/../Cards/cards.inc.php';

    $playerDecks = Globals::getPlayerDecks()[$pId] ?? [];
    $deckInfo = $playerDecks[$deckNumber] ?? null;
    if ($deckInfo === null) {
      return null;
    }

    $demoDef = null;
    foreach (DEMO_ROC_DECKS as $deck) {
      if ($deck['deckId'] === ($deckInfo['deckId'] ?? null)) {
        $demoDef = $deck;
        break;
      }
    }
    if ($demoDef === null) {
      return null;
    }

    $hero = Cards::getFiltered($pId, "deck-$deckNumber", HERO)->first();
    if ($hero === null) {
      return null;
    }

    $deckContent = [];
    $deckContent[HERO] = ['card' => ['properties' => $hero->getProperties()], 'n' => 1];
    foreach ($demoDef['contents'] as $cardId => $n) {
      $factionSub = substr($cardId, 0, 2);
      $className = "\\ALT\\Cards\\$factionSub\\$cardId";
      $card = new $className(null);
      if ($card->getType() == HERO || $card->isToken()) {
        continue;
      }
      $deckContent[] = ['card' => ['properties' => $card->getProperties()], 'n' => $n];
    }

    return [
      'deckName' => $hero->getName(),
      'faction' => $deckInfo['faction'],
      'cards' => $deckContent,
    ];
  }

  function getStarterDeckPreview($pId, $deckNumber)
  {
    require_once dirname(__FILE__) . '/../Cards/cards.inc.php';

    $playerDecks = Globals::getPlayerDecks()[$pId] ?? [];
    $deckInfo = $playerDecks[$deckNumber] ?? null;
    if ($deckInfo === null) {
      return null;
    }

    $starterDef = null;
    foreach (STARTER_DECKS as $deck) {
      if ($deck['deckId'] === ($deckInfo['deckId'] ?? null)) {
        $starterDef = $deck;
        break;
      }
    }
    if ($starterDef === null) {
      return null;
    }

    $hero = Cards::getFiltered($pId, "deck-$deckNumber", HERO)->first();
    if ($hero === null) {
      return null;
    }

    $deckContent = [];
    $deckContent[HERO] = ['card' => ['properties' => $hero->getProperties()], 'n' => 1];
    foreach ($starterDef['contents'] as $cardId => $n) {
      $factionSub = substr($cardId, 0, 2);
      $className = "\\ALT\\Cards\\$factionSub\\$cardId";
      $card = new $className(null);
      if ($card->getType() == HERO || $card->isToken()) {
        continue;
      }
      $deckContent[] = ['card' => ['properties' => $card->getProperties()], 'n' => $n];
    }

    return [
      'deckName' => $hero->getName(),
      'faction' => $deckInfo['faction'],
      'cards' => $deckContent,
    ];
  }

  public function actSelectRandomDeck($faction)
  {
    $this->gamestate->checkPossibleAction('actSelectRandomDeck');

    $faction = $this->normalizeFactionForRandomDeck($faction);
    $deckContent = Cards::buildRandomDeckContent($faction);

    $player = Players::getCurrent();
    $pId = $player->getId();
    $gContent = Globals::getDeckContent();
    $gContent[$pId] = [
      'cards' => $deckContent,
      'faction' => $faction,
      'deckName' => clienttranslate('Random deck'),
      'random' => true,
    ];
    Globals::setDeckContent($gContent);

    $selection = Globals::getDeckSelection();
    $selection[$pId] = 'random';
    Globals::setDeckSelection($selection);
    Notifications::updateInitialPrecoDeckSelection($player, $this->argsPrecoDeckSelection());

    $this->updateActivePlayersPrecoDeckSelection();
  }

  private function normalizeFactionForRandomDeck($faction)
  {
    if ($faction === 'OR') {
      $faction = FACTION_OD;
    }
    if ($faction === 'ALL' || $faction === '' || is_null($faction)) {
      return FACTIONS[array_rand(FACTIONS)];
    }
    if (!in_array($faction, FACTIONS, true)) {
      throw new \BgaUserException(clienttranslate('Invalid faction for random deck'));
    }
    return $faction;
  }
  
  function argsPrecoDeckSelection()
  {
    $isDemoDeckFormat = Globals::getDeckFormat() == 'DEMO';
    $args = [
      '_private' => [],
      'demoDeck' => $isDemoDeckFormat,
    ];
    $allDecks = Globals::getPlayerDecks();
    $selection = Globals::getDeckSelection();
    foreach (Players::getAll() as $pId => $player) {
      $decks = $allDecks[$pId] ?? [];
      foreach ($decks as &$deck) {
        $deck['hero'] = $this->getDeckHero($deck['deckNumber']);
      }
      $args['_private'][$pId]['decks'] = $decks;
      $args['_private'][$pId]['selection'] = $selection[$pId] ?? null;

      if ($args['_private'][$pId]['selection'] == 'API') {
        $gContent = Globals::getDeckContent();
        $args['_private'][$pId]['API'] = $gContent[$pId] ?? null;
      }
      
      if ($args['_private'][$pId]['selection'] == 'random') {
        $gContent = Globals::getDeckContent();
        $args['_private'][$pId]['randomDeck'] = $gContent[$pId] ?? null;
      }
      
      $selectionVal = $args['_private'][$pId]['selection'];
      if ($selectionVal !== null && $selectionVal !== 'API' && $selectionVal !== 'random') {
        if ($isDemoDeckFormat) {
          $args['_private'][$pId]['starterDeck'] = $this->getDemoDeckPreview($pId, $selectionVal);
        } else {
          $args['_private'][$pId]['starterDeck'] = $this->getStarterDeckPreview($pId, $selectionVal);
        }
      }
    }

    return $args;
  }

  public function actSelectPrecoDeck($choice)
  {
    $this->gamestate->checkPossibleAction('actSelectPrecoDeck');

    $player = Players::getCurrent();
    $selection = Globals::getDeckSelection();
    $selection[$player->getId()] = $choice;
    Globals::setDeckSelection($selection);
    Notifications::updateInitialPrecoDeckSelection($player, $this->argsPrecoDeckSelection());

    $this->updateActivePlayersPrecoDeckSelection();
  }

  public function actCancelPrecoDeckSelection()
  {
    $this->gamestate->checkPossibleAction('actCancelPrecoDeckSelection');

    $player = Players::getCurrent();
    $selection = Globals::getDeckSelection();
    unset($selection[$player->getId()]);
    Globals::setDeckSelection($selection);

    // Re-mark this player as multiactive BEFORE notifying them: otherwise the client
    // processes the notification (and tries to re-enter the fetchDecks flow) while BGA
    // still thinks this player is inactive, and silently skips onEnteringState for it.
    $this->updateActivePlayersPrecoDeckSelection();
    Notifications::updateInitialPrecoDeckSelection($player, $this->argsPrecoDeckSelection());
  }

  public function updateActivePlayersPrecoDeckSelection()
  {
    // Compute players that still need to select their card
    // => use that instead of BGA framework feature because in some rare case a player
    //    might become inactive eventhough the selection failed (seen in Agricola and Rauha at least already)
    $selection = Globals::getDeckSelection();
    // TEMPORARY PATCH : some players confirmed their deck with no deck stored => cancel that
    $deckContent = Globals::getDeckContent();
    foreach ($selection as $pId => $choice) {
      if ($choice == 'API' && !isset($deckContent[$pId])) {
        unset($selection[$pId]);
      }
    }
    //////////////////////////////////////

    $players = Players::getAll();
    $ids = $players->getIds();
    $ids = array_diff($ids, array_keys($selection));

    // At least one player need to make a choice
    if (!empty($ids)) {
      $this->gamestate->setPlayersMultiactive($ids, 'done', true);
    }
    // Everyone is done => discard cards and proceed
    else {
      $this->gamestate->nextState('done');
    }
  }

  ///////////////////////////////////////////////////
  //     _    ____ ___   ____            _        
  //    / \  |  _ \_ _| |  _ \  ___  ___| | _____ 
  //   / _ \ | |_) | |  | | | |/ _ \/ __| |/ / __|
  //  / ___ \|  __/| |  | |_| |  __/ (__|   <\__ \
  // /_/   \_\_|  |___| |____/ \___|\___|_|\_\___/
  ///////////////////////////////////////////////////

  /**
   * Get list of decks
   * - page
   * - filter string
   * - faction TODO
   * - hero TODO
   */
  public function actLoaAPIdDecks($request)
  {
    // Default value of parameters
    $request['name'] = $request['name'] ?? '';
    $request['page'] = $request['page'] ?? 1;
    $request['factions'] = $request['factions'] ?? ['AX', 'BR', 'MU', 'LY', 'OR', 'YZ'];
    // $request['factions'] = ['AX'];
    $request['hero'] = $request['hero'] ?? '';

    $tableId = (string) $this->table_id;
    $tournamentInfo = [];
    if ($this->bga->tournament->isTournament()) {
      $tournamentInfo = $this->bga->tournament->getInfo();
    }
    $ratingMode = $this->tableOptions->get(201);
    $request['eventFormat'] = base64_encode(json_encode([
      'v' => 1,
      'env' => $this->getGameName(),
      'mode' => $ratingMode,
      'payload' => [
        'format' => Globals::getDeckFormat(),
        'tableId' => $tableId,
        'tournamentName' => $tournamentInfo['name'] ?? null,
        'tournamentParentId' => $tournamentInfo['tournament_parent_id'] ?? null,
        'tournamentGroup' => $tournamentInfo['tournament_group'] ?? null,
      ],
    ]));

    // STANDARD, NO_UNIQUE, SINGLETON
    // throw new \feException(print_r($request));
    // Fetch them from MS
    $response = self::getGenericGameInfos('get_player_decks', $request);
    if ($response['success'] != 1) {
      throw new \Bga\GameFramework\VisibleSystemException("API ERROR###" . $response['message'] . "###");
    }
    $content = $response['content'];
    $content['request'] = $request;

    // Add infos to the deck
    foreach ($content['decks'] as &$deck) {
      $card = Cards::getCardClass($deck['hero']);
      $deck['heroName'] = $card->getName();
      $deck['heroThumbnail'] = $card->getThumbnail();
    }

    return $content;
  }



  public function actGetDeckInfos($deckNumber)
  {
    $tableId = (string) $this->table_id;
    $tournamentInfo = [];
    if ($this->bga->tournament->isTournament()) {
      $tournamentInfo = $this->bga->tournament->getInfo();
    }
    $ratingMode = $this->tableOptions->get(201);
    $request = base64_encode(json_encode([
      'v' => 1,
      'env' => $this->getGameName(),
      'mode' => $ratingMode,
      'payload' => [
        'playerId' => Players::getCurrentId(),
        'playerName' => Players::getCurrent()->getName(),
        'deckId' => $deckNumber,
        'format' => Globals::getDeckFormat(),
        'tableId' => $tableId,
        'tournamentName' => $tournamentInfo['name'] ?? null,
      ],
    ]));

    $response = self::getGenericGameInfos('get_player_deck_content', ['deck_id' => $request]);
    if ($response['success'] != 1) {
      throw new \Bga\GameFramework\VisibleSystemException("API ERROR###" . $response['message'] . "###");
    }
    $deck = $response['content'];
    $deckContent = [];

    if ($deck['legality'] != true) {
      throw new \BgaUserException(clienttranslate('This deck is not legal'));
    }

    /////////////////////////////////////////
    // Changing all alt-art into basic art
    /////////////////////////////////////////
    $deck[HERO] = $this->transformingAltArtToBasicArt($deck[HERO]);
    $cards = $deck['cards'];
    $sanitizeAltArtCards = [];
    foreach ($cards as $ref => $quantity) {
      $sanitizeAltArtCards[$this->transformingAltArtToBasicArt($ref)] = $quantity;
      unset($cards[$ref]);
    }
    $deck['cards'] = $sanitizeAltArtCards;
    /////////////////////////////////////////
    // Changing all alt-art into basic art
    /////////////////////////////////////////

    $deckContent[HERO] = ['card' => Cards::getCardClass($deck[HERO]), 'n' => 1];
    foreach ($deck['cards'] as $cardRef => $card) {
      if (isset($card['content'])) {
        //it's a unique!
        if (is_null(Cards::generateUnique($card['content']))) {
          throw new \BgaUserException(
            'This unique has an unimplemented power' . $card['content']['reference']
          );
        }
        if (in_array($card['content']['name'], ['Moonlight Jellyfish'])) {
          // if ($card['content']['name'] == 'Foundry Armorer' && $card['content']['faction'] != 'BR') {
          //   $toto = "titit";
          // } else {
          throw new \BgaUserException(clienttranslate(sprintf(self::_("The unique %s is temporarily suspended by Equinox"), $card['content']['name'])));
          // }
        }

        $deckContent[] = ['card' => ['properties' => Cards::generateUnique($card['content'])], 'n' => 1];
      } else {
        $cProp = Cards::getCardClass($cardRef)->getProperties();
        $deckContent[] = ['card' => ['properties' => $cProp], 'n' => $card['quantity']];
      }
    }
    $deck['cards'] = $deckContent;
    $gContent = Globals::getDeckContent();
    $gContent[Players::getCurrentId()] = $deck;
    Globals::setDeckContent($gContent);

    return $deck;
  }

  public function actConfirmAPIDeck($deckContent)
  {
    $this->gamestate->checkPossibleAction('actConfirmAPIDeck');

    $gContent = Globals::getDeckContent();
    $gContent[Players::getCurrentId()] = $deckContent;
    Globals::setDeckContent($gContent);

    $player = Players::getCurrent();
    $selection = Globals::getDeckSelection();
    $selection[$player->getId()] = 'API';
    Globals::setDeckSelection($selection);
    Notifications::updateInitialPrecoDeckSelection($player, $this->argsPrecoDeckSelection());

    $this->updateActivePlayersPrecoDeckSelection();
  }


  /////////////////////////////////////////////////////////
  //  ____       _                     _           _
  // / ___|  ___| |_ _   _ _ __     __| | ___  ___| | __
  // \___ \ / _ \ __| | | | '_ \   / _` |/ _ \/ __| |/ /
  //  ___) |  __/ |_| |_| | |_) | | (_| |  __/ (__|   <
  // |____/ \___|\__|\__,_| .__/   \__,_|\___|\___|_|\_\
  //                      |_|
  /////////////////////////////////////////////////////////

  function stDeckSetup()
  {
    $selection = Globals::getDeckSelection();
    $factionMap = [FACTION_AX => 1, FACTION_BR => 2, FACTION_LY => 3, FACTION_MU => 4, FACTION_OD => 5, 'OR' => 5, FACTION_YZ => 6];
    $factions = [];
    foreach (Players::getAll() as $pId => $player) {
      if ($selection[$pId] == 'API') {
        $deckContent = Globals::getDeckContent()[$pId]['cards'] ?? null;
        if (is_null($deckContent)) {
          throw new \feException(clienttranslate('Your deck is not valid. It should not happen'));
        }
        $faction = Cards::createDeck($player, $deckContent);
      } elseif ($selection[$pId] == 'random') {
        $stored = Globals::getDeckContent()[$pId] ?? null;
        if (!empty($stored['cards'])) {
          $faction = Cards::createDeck($player, $stored['cards']);
        } else {
          $faction = Cards::generateRandomDeck([], $player);
        }
        $selection[$pId] = 'API';
      } else {
        $deckNumber = $selection[$pId];
        // faction setup
        $faction = Globals::getPlayerDecks()[$pId][$deckNumber]['faction'];
      }
      $faction = substr($faction, 0, 2);
      $player->setFaction($faction);
      $factions[$pId] = $faction;
      Stats::setFaction($player, $factionMap[$faction] ?? 1);
    }
    Notifications::vsScreen($factions);

    foreach (Players::getAll() as $pId => $player) {
      $deckNumber = $selection[$pId];

      // delete all other cards
      $toDel = Cards::getFiltered($pId)->whereNot('location', "deck-$deckNumber");
      Cards::delete($toDel->getIds());

      // updating Cards
      $hero = $this->getDeckHero($deckNumber);
      $hero->setLocation("board-hero-$pId");
      Cards::getFiltered($pId)
        ->where('location', "deck-$deckNumber")
        ->update('location', "deck-$pId");

      $meeples = Meeples::setupPlayer($player);

      // shuffling of dec
      Cards::shuffle("deck-$pId");
      Notifications::setupDeck($player, $meeples, $hero);
    }
    Players::setStats();

    // TODO : remove ???
    //    Notifications::setupCards(Cards::getUiData());

    $this->gamestate->nextState('');
  }

  /////////////////////////////////////////////////////////
  // Temporary Transforming all Alt-art card into the basic art one.
  // We dont want to display alt-art into BGA for the transitionn period to Re:Union until we get players collections back.
  /////////////////////////////////////////////////////////
  private function transformingAltArtToBasicArt(string $cardRef): string
  {
    // e.g. ALT_CYCLONE_A_BR_77_C -> ALT_CYCLONE_B_BR_77_C ; changing the 3rd part 'A' alt-art to 'B' basic-art

    // Commented because all alt-art are not all present in getAltArt
    // if (key_exists($cardRef, Cards::getAltArt())) {

    $cardExploded = explode('_', $cardRef);
    $cardRefWithoutRarity = implode(
      '_',
      [$cardExploded[0], $cardExploded[1], $cardExploded[2], $cardExploded[3], $cardExploded[4]]
    );

    // Exception
    // ALT_ALIZE_P_OR_48 and ALT_BISE_P_BR_64
    if (in_array($cardRefWithoutRarity, ['ALT_ALIZE_P_OR_48', 'ALT_BISE_P_BR_64'])) {
      return $cardRef;
    }

    // Musubi Specific case
    if ($cardExploded[1] === 'MUSUBI') {
      $musubiCorrespondingCards = [
        'ALT_MUSUBI_B_AX_09' => 'ALT_CORE_B_AX_09',
        'ALT_MUSUBI_B_BR_04' => 'ALT_CORE_B_BR_04',
        'ALT_MUSUBI_B_LY_06' => 'ALT_CORE_B_LY_06',
        'ALT_MUSUBI_B_MU_22' => 'ALT_CORE_B_MU_22',
        'ALT_MUSUBI_B_OR_09' => 'ALT_CORE_B_OR_09',
        'ALT_MUSUBI_B_YZ_09' => 'ALT_CORE_B_YZ_09',
        'ALT_MUSUBI_B_AX_30' => 'ALT_CORE_B_AX_30',
        'ALT_MUSUBI_B_BR_74' => 'ALT_CYCLONE_B_BR_74',
        'ALT_MUSUBI_B_LY_29' => 'ALT_CORE_B_LY_29',
        'ALT_MUSUBI_B_MU_70' => 'ALT_CYCLONE_B_MU_70',
        'ALT_MUSUBI_B_OR_66' => 'ALT_CYCLONE_B_OR_66',
        'ALT_MUSUBI_B_YZ_21' => 'ALT_CORE_B_YZ_21',
      ];

      if (key_exists($cardRefWithoutRarity, $musubiCorrespondingCards)) {
        $cardRef = implode('_', [$musubiCorrespondingCards[$cardRefWithoutRarity], $cardExploded[5]]);
      }
    } elseif ($cardExploded[1] === 'DUSTERTOP') {
      // DusterTop Specific case
      $dustertopCorrespondingCards = [
        // CORE Set
        'ALT_DUSTERTOP_P_AX_04_C' => 'ALT_CORE_B_AX_04_C',
        'ALT_DUSTERTOP_P_AX_20_R1' => 'ALT_CORE_B_AX_20_R1',
        'ALT_DUSTERTOP_P_BR_19_C' => 'ALT_CORE_B_BR_19_C',
        'ALT_DUSTERTOP_P_BR_30_R1' => 'ALT_CORE_B_BR_30_R1',
        'ALT_DUSTERTOP_P_LY_07' => 'ALT_CORE_B_AX_04_C',
        'ALT_DUSTERTOP_P_LY_04_R1' => 'ALT_CORE_B_AX_04_R1',
        'ALT_DUSTERTOP_P_MU_13_C' => 'ALT_CORE_B_AX_04_C',
        'ALT_DUSTERTOP_P_MU_12_R1' => 'ALT_CORE_B_AX_04_R1',
        'ALT_DUSTERTOP_P_OR_14_C' => 'ALT_CORE_B_OR_14_C',
        'ALT_DUSTERTOP_P_OR_08_R1' => 'ALT_CORE_B_OR_08_R1',
        'ALT_DUSTERTOP_P_YZ_06_C' => 'ALT_CORE_B_YZ_06_C',
        'ALT_DUSTERTOP_P_YZ_12_R1' => 'ALT_CORE_B_YZ_12_R1',
        // ALIZE Set
        'ALT_DUSTERTOP_P_AX_41_C' => 'ALT_ALIZE_B_AX_41_C',
        'ALT_DUSTERTOP_P_AX_32_R1' => 'ALT_ALIZE_B_AX_32_R1',
        'ALT_DUSTERTOP_P_BR_32_C' => 'ALT_ALIZE_B_BR_32_C',
        'ALT_DUSTERTOP_P_BR_38_R1' => 'ALT_ALIZE_B_BR_38_R1',
        'ALT_DUSTERTOP_P_LY_31_C' => 'ALT_ALIZE_B_LY_31_C',
        'ALT_DUSTERTOP_P_LY_39_R1' => 'ALT_ALIZE_B_LY_39_R1',
        'ALT_DUSTERTOP_P_MU_44_C' => 'ALT_ALIZE_B_MU_44_C',
        'ALT_DUSTERTOP_P_MU_33_R1' => 'ALT_ALIZE_B_MU_33_R1',
        'ALT_DUSTERTOP_P_OR_42_C' => 'ALT_ALIZE_B_OR_42_C',
        'ALT_DUSTERTOP_P_OR_43_R1' => 'ALT_ALIZE_B_OR_43_R1',
        'ALT_DUSTERTOP_P_YZ_41_C' => 'ALT_ALIZE_B_YZ_41_C',
        'ALT_DUSTERTOP_P_YZ_44_R1' => 'ALT_ALIZE_B_YZ_44_R1',
      ];
      if (key_exists($cardRef, $dustertopCorrespondingCards)) {
        $cardRef = $dustertopCorrespondingCards[$cardRef];
      }
    } else {
      // Normal handling, just replacing the Alt or Promo tag by the Basic tag
      if (in_array($cardExploded[2], ['A', 'P'])) {
        $cardExploded[2] = 'B';
      }

      // Replacing PromoSet by corresponding Basic set (when corresponding to only one set)
      $promoSetsToReplace = [
        'COREKS' => 'CORE',
        'WCF25' => 'CORE',
        'WCS25' => 'CORE',
        'WCQ25' => 'CORE',
        'JUDGE' => 'CORE',
        'TCS3' => 'BISE',
        'DUSTEROP' => 'DUSTER',
        'DUSTERCB' => 'DUSTER',
        'WCS26' => 'DUSTER',
      ];
      if (key_exists($cardExploded[1], $promoSetsToReplace)) {
        $cardExploded[1] = $promoSetsToReplace[$cardExploded[1]];
      }

      $cardRef = implode('_', $cardExploded);
    }

    return $cardRef;
  }
}
