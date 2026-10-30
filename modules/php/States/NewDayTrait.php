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
use ALT\Helpers\FT;

trait NewDayTrait
{
  /****************** FIRST DAY*************/
  function stFirstDay()
  {
    $day = Globals::incDay(1);
    Stats::incDays();
    $nCards = 6;
    if (Globals::getTestingOption()) {
      $nCards = 15;
    }

    $markers = Meeples::createHeroMarkers();
    if (!empty($markers)) {
      Notifications::addTerrainMarkers($markers);
    }

    // Draw cards and make everyone active
    $pIds = [];

    // on new day, x cards are picked
    foreach (Players::getAll() as $pId => $player) {
      // put in specific location as they must be choosen
      $player->draw($nCards);
      $pIds[] = $pId;
    }

    Globals::setFirstDayManaSelection([]);
    $this->gamestate->setPlayersMultiactive($pIds, '', true);
    $this->gamestate->nextState('');
  }

  // Choose card(s) to put as mana
  function argsFirstDayManaSelection()
  {
    $selection = Globals::getFirstDayManaSelection();
    $args = [
      'canPass' => false,
      '_private' => [],
      'n' => Globals::getTestingOption() ? 10 : 3,
    ];

    foreach (Players::getAll() as $pId => $player) {
      $hand = $player->getHand();
      $args['_private'][$pId] = [
        'n' => Globals::getTestingOption() ? 10 : 3,
        'cards' => $hand->getIds(),
        'selection' => $selection[$pId] ?? null,
      ];
    }

    return $args;
  }

  public function actFirstDayManaSelection($cardIds)
  {
    self::localCheckAction('actFirstDayManaSelection');
    $player = Players::getCurrent();
    $args = $this->argsFirstDayManaSelection()['_private'][$player->getId()];

    if (count($cardIds) != $args['n']) {
      throw new \BgaUserException(clienttranslate('You must select the correct number of cards to put as mana'));
    }
    if (!empty(array_diff($cardIds, $args['cards']))) {
      throw new \BgaUserException('Invalid card to select. Should not happen');
    }

    $selection = Globals::getFirstDayManaSelection();
    $selection[$player->getId()] = $cardIds;
    Globals::setFirstDayManaSelection($selection);
    Notifications::updateFirstDayManaSelection($player, self::argsFirstDayManaSelection());

    $this->updateActivePlayersFirstDayManaSelection();
  }

  public function actCancelFirstDayManaSelection()
  {
    $this->gamestate->checkPossibleAction('actCancelFirstDayManaSelection');

    $player = Players::getCurrent();
    $selection = Globals::getFirstDayManaSelection();
    unset($selection[$player->getId()]);
    Globals::setFirstDayManaSelection($selection);
    Notifications::updateFirstDayManaSelection($player, self::argsFirstDayManaSelection());

    $this->updateActivePlayersFirstDayManaSelection();
  }

  public function updateActivePlayersFirstDayManaSelection()
  {
    // Compute players that still need to select their card
    // => use that instead of BGA framework feature because in some rare case a player
    //    might become inactive eventhough the selection failed (seen in Agricola and Rauha at least already)
    $selection = Globals::getFirstDayManaSelection();
    $players = Players::getAll();
    $ids = $players->getIds();
    $ids = array_diff($ids, array_keys($selection));

    // At least one player need to make a choice
    if (!empty($ids)) {
      $this->gamestate->setPlayersMultiactive($ids, 'done', true);
    }
    // Everyone is done => discard cards and proceed
    else {
      $selection = Globals::getFirstDayManaSelection();
      foreach ($players as $pId => $player) {
        $cardIds = $selection[$pId];
        if (empty($cardIds)) {
          continue;
        }

        Cards::discard($cardIds, MANA);
        $cards = Cards::getMany($cardIds);
        Notifications::discardMana($player, $cards, null, clienttranslate('${player_name} chooses ${n} card(s) as mana'));
      }

      $this->checkCardListeners('Noon', ST_BEFORE_ASSIGNMENT);
    }
  }

  /*************************** NEW DAY**********************/

  // Clear previous stuff and draw new cards
  function stNewDay()
  {
    $this->initCustomTurnOrder('morning', [Players::getActiveId()], 'stNewDayInit', 'stCheckMorning');
  }

  function stNewDayInit()
  {
    if (Players::checkVictory()) {
      return;
    }
    if (Globals::isEnterTieBreakerMode()) {
      Globals::setTieBreakerMode(true);
      Globals::setEnterTieBreakerMode(false);
    }

    Globals::incDay(1);
    Globals::setSkippedPlayers([]);
    Globals::setStormMoves([]);
    Globals::setExpeditionMoves([]);
    Globals::setNextCharacterBoost(0);
    Globals::setNextCharacterBoostOccurence(0);
    Globals::setNextAnimalBoost(0);
    Globals::setNextAnimalBoostOccurence(0);
    Globals::setNextReserveCharacterBoost(0);
    Globals::setBlockedExpeditions([]);
    Globals::setGlobalTough([]);
    Globals::setFirstPass(-1);
    Globals::setTurnCards([]);
    Globals::setConstructionPlayedThisDay([]);
    Globals::setBlockedCardNamesThisDay([]);
    Globals::setDieRolledThisDay([]);
    Globals::setNextCharacterInExpeditionBoost([]);
    Cards::untapAll();
    Stats::incDays();
    Notifications::updateTotalMana();
    Globals::setPhase(0);
    Notifications::newPhase(PHASE_MORNING);

    // Change first player
    $currentFirstPId = Globals::getFirstPlayer();
    $newFirstPId = Players::getNextId($currentFirstPId);
    Globals::setFirstPlayer($newFirstPId);
    Notifications::newFirstPlayer(Players::get($newFirstPId));

    $nodes = [];

    foreach (Players::getAll() as $player) {
      $node = [];
      if ($player->getHero()->getUid() == 'ALT_CORE_B_LY_03_C') {
        if ($player->getDeckCount() == 0) {
          // interrupt as deck is empty
          $node[] = FT::SEQ_OPTIONAL(
            FT::ACTION(SHUFFLE, [], ['pId' => $player->getId(), 'optional' => true]),
            FT::ACTION(DRAW, ['n' => 1, 'players' => ME], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()]),
            FT::ACTION(DRAW_MANA, ['n' => 1, 'tapped' => false], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()])
          );
        } elseif ($player->getDeckCount() == 1) {
          // interrupt as only 1 card in deck
          $node[] = FT::SEQ(
            FT::ACTION(DRAW, ['n' => 1, 'players' => ME], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()]),
            FT::SEQ_OPTIONAL(
              FT::ACTION(SHUFFLE, [], ['pId' => $player->getId(), 'optional' => true]),
              FT::ACTION(DRAW_MANA, ['n' => 1, 'tapped' => false], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()])
            )
          );
        } elseif ($player->getDeckCount() == 2) {
          // interrupt as only 1 card in deck
          $node[] = FT::SEQ(
            FT::ACTION(DRAW, ['n' => 1, 'players' => ME], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()]),
            FT::ACTION(DRAW_MANA, ['n' => 1, 'tapped' => false], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()]),
            FT::SEQ_OPTIONAL(FT::ACTION(SHUFFLE, [], ['pId' => $player->getId(), 'optional' => true]),),
          );
        } else {
          $player->draw(1, null, null, $player->getHero());
          $player->draw(
            1,
            null,
            MANA,
            $player->getHero(),
            clienttranslate('${player_name} draws 1 card from its deck and put it in mana (${card_name2}\'s effect)'),
            clienttranslate('You draw ${card_names} from your deck and put it in mana (${card_name2}\'s effect)')
          );
        }
        $skipped[] = $player->getId();
        Globals::setSkippedPlayers($skipped);
      } else {
        if ($player->getDeckCount() == 0) {
          $node[] = FT::SEQ_OPTIONAL(
            FT::ACTION(SHUFFLE, [], ['pId' => $player->getId(), 'optional' => true]),
            FT::ACTION(DRAW, ['n' => 2, 'players' => ME], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()])
          );
        } elseif ($player->getDeckCount() == 1) {
          // interrupt as only 1 card in deck
          $node[] = FT::SEQ(
            FT::ACTION(DRAW, ['n' => 1, 'players' => ME], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()]),
            FT::SEQ_OPTIONAL(
              FT::ACTION(SHUFFLE, [], ['pId' => $player->getId(), 'optional' => true]),
              FT::ACTION(DRAW, ['n' => 1, 'players' => ME], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()]),
            )
          );
        } elseif ($player->getDeckCount() == 2) {
          // interrupt as only 1 card in deck
          $node[] = FT::SEQ(
            FT::ACTION(DRAW, ['n' => 2, 'players' => ME], ['pId' => $player->getId(), 'sourceId' => $player->getHero()->getId()]),
            FT::SEQ_OPTIONAL(FT::ACTION(SHUFFLE, [], ['pId' => $player->getId(), 'optional' => true]),),
          );
        } else {
          $player->draw(2, null, null, $player->getHero());
        }
      }
      if (count($node) > 0) {
        foreach ($node as $n => &$no) {
          $no['pId'] = $player->getId();
        }

        $nodes = array_merge($nodes, $node);
      }
    }
    // throw new \feException(print_r($nodes));
    if (count($nodes) > 0) {
      Engine::setup(FT::SEQ(...$nodes), ['order' => 'morning']);
      Engine::proceed();
    } else {
      // $this->stCheckMorning();
      $this->endCustomOrder('morning');
    }
  }

  public function stCheckMorning()
  {
    // throw new \feException(print_r(debug_print_backtrace()));
    $this->checkCardListeners('Morning', 'initNewDay');
  }

  public function initNewDay()
  {
    $this->initCustomDefaultTurnOrder('newDay', 'stNewDayDraw', 'stAfterNewday', true);
  }

  public function stNewDayDraw()
  {
    $player = Players::getActive();

    // check if a player skipped his turn
    $skipped = Globals::getSkippedPlayers();
    if (in_array($player->getId(), $skipped)) {
      // Everyone is out of round => end it
      $remaining = array_diff(Players::getAll()->getIds(), $skipped);
      if (empty($remaining)) {
        $this->endCustomOrder('newDay');
      } else {
        $this->nextPlayerCustomOrder('newDay');
      }
      return;
    }

    $skipped[] = $player->getId();
    Globals::setSkippedPlayers($skipped);
    self::giveExtraTime($player->getId());

    // Stats::incTurns($player);
    $node = [
      'pId' => $player->getId(),
      'action' => DISCARD,
      'args' => ['canPass' => true, 'n' => 1, 'source' => HAND, 'destination' => MANA],
    ];

    // Inserting leaf Action card
    Engine::setup($node, ['order' => 'newDay']);
    Engine::proceed();
  }

  // Trigger listeners linked to after the noon
  public function stAfterNewDay()
  {
    Globals::setPhase(1);
    Notifications::newPhase(PHASE_NOON);

    // if (Globals::isEnterTieBreakerMode()) {
    //   Globals::setTieBreakerMode(true);
    //   Globals::setEnterTieBreakerMode(false);
    // }
    $this->checkCardListeners('Noon', ST_BEFORE_ASSIGNMENT);
  }
}
