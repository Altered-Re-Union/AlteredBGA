<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Managers\Actions;
use ALT\Core\Engine;
use ALT\Core\Globals;
use ALT\Core\Stats;
use ALT\Helpers\FlowConvertor;
use ALT\Helpers\Utils;
use ALT\Models\Player;
use ALT\Helpers\FT;

class ChooseAssignment extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_CHOOSE_ASSIGNMENT;
  }

  public function getDescription()
  {
    if (count($this->getArg('actions')) == 3) {
      return clienttranslate('Choose an assignment');
    } else {
      return clienttranslate('Play a card');
    }
  }

  protected $args = [
    'types' => [PERMANENT, SPELL, CHARACTER],
    'actions' => ['play', 'support', 'tap'],
    'maxHandCost' => INFTY,
    'minHandCost' => 0,
    'free' => false,
    'maxBaseCost' => INFTY,
    'minBaseCost' => 0,
    'limited' => false,
    'forcedLocation' => null,
    'mandatory' => false,
    'reserveFlipCost' => false,
    'subType' => 'disabled', // Either a string or an array, array uses OR logic
  ];

  /**
   * Free-play cost limits explicitly set on this engine node (not class defaults).
   * Each CHOOSE_ASSIGNMENT in the stack only applies its own limits, so e.g. Santa (≤3 hand)
   * and Daikokuten (≥4 base) or a future ≥4 hand gift do not bleed into one another.
   */
  private function getExplicitFreeCostLimits()
  {
    $ctxArgs = $this->getCtxArgs();
    $keys = ['maxHandCost', 'minHandCost', 'maxBaseCost', 'minBaseCost'];
    $limits = [];
    foreach ($keys as $key) {
      if (array_key_exists($key, $ctxArgs)) {
        $limits[$key] = $ctxArgs[$key];
      }
    }
    return $limits;
  }

  private function cardMeetsExplicitFreeCostLimits($card, array $limits)
  {
    $handCost = $card->getCostHand();
    $baseCost = $card->getLocation() == RESERVE ? $card->getCostReserve() : $card->getCostHand();
    if (isset($limits['minHandCost']) && $handCost < $limits['minHandCost']) {
      return false;
    }
    if (isset($limits['maxHandCost']) && $handCost > $limits['maxHandCost']) {
      return false;
    }
    if (isset($limits['minBaseCost']) && $baseCost < $limits['minBaseCost']) {
      return false;
    }
    if (isset($limits['maxBaseCost']) && $baseCost > $limits['maxBaseCost']) {
      return false;
    }
    return true;
  }


  public function argsChooseAssignment()
  {
    $player = Players::getActive();
    $handCards = $player->getHand();
    $reserveCards = $player->getReserveCards();
    $actions = ['play' => [], 'support' => [], 'tap' => []];
    $authorizedTypes = $this->getArg('types');
    $authorizedActions = $this->getArg('actions');
    $freeCostLimits = $this->getExplicitFreeCostLimits();
    $reserveFlipCost = $this->getArg('reserveFlipCost');
    $free = $this->getArg('free');
    $forcedLocation = $this->getArg('forcedLocation');
    $subType = $this->getArg('subType');
    $matchesSubType = function ($card) use ($subType) {
      if ($subType === 'disabled') {
        return true;
      }
      if (!is_array($subType) && in_array($subType, $card->getSubtypes())) {
        return true;
      }
      return is_array($subType) && count(array_intersect($subType, $card->getSubtypes())) > 0;
    };

    // 1. Play cards
    if (in_array('play', $authorizedActions)) {
      $actions['play'] = $handCards
        ->merge($reserveCards)
                ->filter(function ($card) use ($player, $authorizedTypes, $free, $freeCostLimits, $reserveFlipCost, $matchesSubType) {
          $typeOk = in_array($card->getType(), $authorizedTypes)
            || count(array_intersect($authorizedTypes, $card->getAdditionalType())) > 0;
          if (!$typeOk || !$matchesSubType($card)) {
            return false;
          }
          if (!$free) {
            return $card->canBePlayed($player, false, $reserveFlipCost);
          }
          // Free gifts skip mana cost but keep the same exhausted-reserve rules as canBePlayed /
          // getPlayableLocation (Vaike, Kelonic Heater, …). Empty destinations are dropped below.
          return $card->getMinManaOrbs() <= $player->getTotalMana()
            && !$card->isExhaustedReservePlayBlocked($player)
            && $this->cardMeetsExplicitFreeCostLimits($card, $freeCostLimits);
        })
        ->map(function ($card) use ($player, $forcedLocation, $free) {
          return $card->getPlayableLocation($player, $forcedLocation, $free);
        })
        // Cards with no legal zone (e.g. exhausted without Vaike) must not appear as selectable.
        ->filter(function ($locations) {
          return !empty($locations);
        });
      // Scout is only for hand cards
      $scouts = $handCards
        ->filter(function ($card) use ($player, $authorizedTypes, $free, $freeCostLimits, $matchesSubType) {
          $meetsFreeScoutCost = $free
            && $this->cardMeetsExplicitFreeCostLimits($card, $freeCostLimits);
          return $card->getScout() > 0
            && in_array($card->getType(), $authorizedTypes)
            && $matchesSubType($card)
            && ((!$free && $card->canBePlayed($player, true)) || $meetsFreeScoutCost);
        })
        ->map(function ($card) use ($player, $forcedLocation) {
          return $card->getScoutableLocations($player, $forcedLocation);
        });
      foreach ($scouts as $key => $locs) {
        $actions['play'][$key] = array_merge($actions['play'][$key] ?? [], $locs);
      }
      // $actions['play'] = $actions['play']; //->merge($scouts);
      $actions['toto'] = $scouts;
    }

    // 2. Support
    if (in_array('support', $authorizedActions)) {
      $actions['support'] = $reserveCards
        ->filter(function ($card) use ($player) {
          return !empty($card->getEffectSupport()) &&
            !empty($card->getSupportDesc()) &&
            !$card->isTapped();
        })
        ->getIds();
    }

    // 3. Tap effect
    if (in_array('tap', $authorizedActions)) {
      $actions['tap'] = $player
        ->getPlayedCards()
        ->merge($player->getHeroCollection())
        ->filter(function ($card) use ($player) {
          $effectTap = $card->getEffectTap();
          if (is_null('effectTap') || empty($effectTap)) {
            return false;
          }
          $effectTap['sourceId'] = $card->getId();
          return !$card->isTapped() &&
            Engine::buildTree($effectTap)->isDoable($player);
        })
        ->getIds();
    }
    $additionalAction = count($this->getArg('actions')) != 3;

    return ['_private' => ['active' => $actions], 'additionalAction' => $additionalAction, 'descSuffix' => $additionalAction ? 'additional' : ''];
  }

  public function isOptional($player)
  {
    return $this->getCtx()->getOptional() == true || (count($this->getArg('actions')) != 3 && empty($this->argsChooseAssignment()['_private']['active']['play']->toArray() ?? []) && !$this->getArg('mandatory'));
  }

  public function isDoable($player)
  {
    if ($this->isOptional($player) || !$this->getArg('mandatory')) {
      return true;
    }

    if (count($this->getArg('actions')) != 3 && !empty($this->argsChooseAssignment()['_private']['active']['play']->toArray() ?? [])) {
      return true;
    }

    return false;
  }

  public static function statPlay($carId)
  {
    $player = Players::getActive();
    $statMapping = Globals::getStatMapping()[$player->getId()] ?? null;
    if (is_null($statMapping) || !isset($statMapping[$carId])) {
      return;
    }

    $f = 'get' . ucfirst($statMapping[$carId]);
    $stat = Stats::$f($player);

    if ($stat < 200000) {
      // we flag the card as played
      $f = 'set' . ucfirst($statMapping[$carId]);
      Stats::$f($player, $stat + 100000);
    }
  }

  ///////////////////////////
  //  ____  _
  // |  _ \| | __ _ _   _
  // | |_) | |/ _` | | | |
  // |  __/| | (_| | |_| |
  // |_|   |_|\__,_|\__, |
  //                |___/
  ///////////////////////////

  public function actPlay($cardId, $location)
  {
    $scout = false;
    $args = $this->argsChooseAssignment()['_private']['active']['play'];
    $locations = $args[$cardId] ?? null;
    if (is_null($locations)) {
      throw new \BgaVisibleSystemException('This card cannot be played. Should not happen');
    }
    if (!in_array($location, $locations)) {
      throw new \BgaVisibleSystemException('Invalid location to play a card. Should not happen');
    }
    $locExploded = explode('_', $location);
    if ($locExploded[1] ?? '' == 'scout') {
      $scout = true;
    }

    $this->playCard($cardId, $location, $this->getArg('free'), true, 0, true, $scout);
  }

  public function playCard($cardId, $location, $free = false, $effectHand = true, $newCost = 0, $reallyPlayed = true, $scout = false, $stealOwnership = false, $countsAsTurnPlay = null) 
  {
    $player = Players::getActive();
    $card = Cards::get($cardId);
    $location = explode('_', $location)[0];

    if ($card->getPId() != $player->getId()) {
      throw new \BgaVisibleSystemException('You do not own this card. Should not happen');
    }

    if ($free == false) {
      // Calculate cost
      $cost = $card->getCost($scout, $this->getArg('reserveFlipCost'), $location);
      // Diocles Chariot racer Rare
      if ($card->getPlayLimitation() == '+3StartingRegion') {
        if ($location == STORM_LEFT && $player->getHeroToken()->getLocation() == 'storm-0') {
          $cost += 3;
        }
        if ($location == STORM_RIGHT && $player->getCompanionToken()->getLocation() == 'storm-7') {
          $cost += 3;
        }
      }
      // Lyra DJ
      if ($card->getPlayLimitation() == '-2Contact' && $player->isInContact($location)) {
        $cost -= 2;
      } elseif ($card->getPlayLimitation() == '-2Multi') {
        $opponent = Players::getNext($player);
        if ($opponent->countCardsInLocation(STORM_LEFT, CHARACTER) || $opponent->countCardsInLocation(STORM_RIGHT, CHARACTER)) {
          $cost -= 2;
        }
      }
      $costReduction = Globals::getCostReduction();
      foreach (($costReduction[$player->getId()] ?? []) as $costType => $reductionCost) {
        if (!is_array($reductionCost)) {
          continue;
        }
        if ($card->getType() == $costType || in_array($costType, $card->getAdditionalType()) || $costType == ALL) {
          unset($costReduction[$player->getId()][$costType]);
        }
      }

      foreach ($card->getSubtypes() as $subtype) {
        if (isset($costReduction[$player->getId()][$subtype])) {
          unset($costReduction[$player->getId()][$subtype]);
        }
      }

      if (isset($costReduction[$player->getId()][ALL])) {
        unset($costReduction[$player->getId()][ALL]);
      }
      Globals::setCostReduction($costReduction);

      // management of CostReductionDiscard, discarding a card from reserve to reduce cost
      if ($card->getCostReductionDiscard() > 0) {
        if (
          ($card->getLocation() == RESERVE && $player->getReserveCards()->count() > 1) ||
          ($card->getLocation() != RESERVE && $player->getReserveCards()->count() > 0)
        ) {
          $this->insertAsChild(
            FT::XOR(
              // next card played needs to be considered as a turn play, even if played for free
              FT::ACTION(PLAY_CARD, ['cardId' => $cardId, 'free' => true, 'location' => $location, 'cost' => $cost, 'countsAsTurnPlay' => true]),
              FT::SEQ(
                FT::ACTION(
                  TARGET,
                  [
                    'targetLocation' => [RESERVE],
                    'targetPlayer' => ME,
                    'excludeSelf' => true,
                    'targetType' => [CHARACTER, TOKEN, SPELL, PERMANENT],
                    'effect' => FT::ACTION(DISCARD, []),
                  ],
                  ['sourceId' => $cardId]
                ),
                FT::ACTION(PLAY_CARD, [
                  'cardId' => $cardId,
                  'free' => true,
                  'cost' => $cost - $card->getCostReductionDiscard(),
                  'location' => $location,
                  'countsAsTurnPlay' => true,
                ])
              )
            )
          );
          $this->resolveAction(['CostReduction']);
          return;
        }
      } elseif ($card->getCostReductionSacrificePermanent() > 0) {
        $nbPermanents = $player->getPlayedCards()->filter(function ($c2) {
          return $c2->getType() == PERMANENT || in_array(PERMANENT, $c2->getAdditionalType());
        })->count();

        if ($nbPermanents > 0) {
          $this->insertAsChild(
            FT::XOR(
              // next card played needs to be considered as a turn play, even if played for free
              FT::ACTION(PLAY_CARD, ['cardId' => $cardId, 'free' => true, 'location' => $location, 'cost' => $cost, 'countsAsTurnPlay' => true]),
              FT::SEQ(
                FT::ACTION(
                  TARGET,
                  [
                    'targetType' => [PERMANENT],
                    'targetPlayer' => ME,
                    'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
                  ],
                  ['sourceId' => $cardId]
                ),
                FT::ACTION(PLAY_CARD, [
                  'cardId' => $cardId,
                  'free' => true,
                  'cost' => $cost - $card->getCostReductionSacrificePermanent(),
                  'location' => $location,
                  'countsAsTurnPlay' => true,
                ])
              )
            )
          );
          $this->resolveAction(['CostReduction']);
          return;
        }
      } elseif ($card->getCostReductionLimitation() > 0) {
        $this->insertAsChild(
          FT::XOR(
            // next card played needs to be considered as a turn play, even if played for free
            FT::ACTION(PLAY_CARD, ['cardId' => $cardId, 'free' => true, 'location' => $location, 'cost' => $cost, 'countsAsTurnPlay' => true]),
            FT::ACTION(PLAY_CARD, ['cardId' => $cardId, 'free' => true, 'location' => $location, 'cost' => $cost - $card->getCostReductionLimitation(), 'limited' => true, 'countsAsTurnPlay' => true]),
          )
        );
        $this->resolveAction(['CostReduction']);
        return;
      } elseif ($card->getCostReductionTap() > 0) {
        if (
          ($card->getLocation() == RESERVE && $player->getReserveCards()->filter(function ($c) {
            return !$c->isTapped();
          })->count() > 1) ||
          ($card->getLocation() != RESERVE && $player->getReserveCards()->filter(function ($c) {
            return !$c->isTapped();
          })->count() > 0)
        ) {
          $this->insertAsChild(
            FT::XOR(
              // next card played needs to be considered as a turn play, even if played for free
              FT::ACTION(PLAY_CARD, ['cardId' => $cardId, 'free' => true, 'location' => $location, 'cost' => $cost, 'countsAsTurnPlay' => true]),
              FT::SEQ(
                FT::ACTION(
                  TARGET,
                  [
                    'targetLocation' => [RESERVE],
                    'targetPlayer' => ME,
                    'excludeSelf' => true,
                    'isNotTapped' => true,
                    'targetType' => [CHARACTER, TOKEN, SPELL, PERMANENT],
                    'effect' => FT::ACTION(EXHAUST, []),
                  ],
                  ['sourceId' => $cardId]
                ),
                // next card played needs to be considered as a turn play, even if played for free
                FT::ACTION(PLAY_CARD, [
                  'cardId' => $cardId,
                  'free' => true,
                  'cost' => $cost - $card->getCostReductionTap(),
                  'location' => $location,
                  'countsAsTurnPlay' => true,
                ])
              )
            )
          );
          $this->resolveAction(['CostReduction']);
          return;
        }
      }
      // Has to update cost here as cost is dynamic where it's played
      if ($card->getCostReductionIfEmpty() > 0 && $player->countCardsInLocation($location, [TOKEN, CHARACTER]) == 0 && !$player->hasGigantic()) {
        $cost -= $card->getCostReductionIfEmpty();
      }

      // Pay cost
      $player->payMana($cost);
    } elseif ($newCost > 0) {
      // Globals::incPlayedCards();
      $player->payMana($newCost);
      $cost = $newCost;
    } else {
      $cost = 0;
    }
    // Effect free plays (Wayfarer, Coppelia, …) use free=true with cost 0 and must not
    // end the Afternoon turn. Cost-reduction PLAY_CARD nodes pass countsAsTurnPlay=true.
    if ($countsAsTurnPlay === null) {
      $countsAsTurnPlay = !($free == true && $cost == 0);
    }
    if ($countsAsTurnPlay) {
      Globals::incPlayedCards();
    }

    $uid = $card->getUid();
    if ($uid !== null) {
      $playedHistory = Globals::getPlayedCardsHistory();
      $playedHistory[$player->getId()][$uid] = ($playedHistory[$player->getId()][$uid] ?? 0) + 1;
      Globals::setPlayedCardsHistory($playedHistory);
    }

    if ((($card->getType() == SPELL || in_array(SPELL, $card->getAdditionalType())) && Globals::isNextSpellIsFree()) || ($free == true && $cost == 0)) {
      Globals::setPlayedForFree(true);
    }
    // Move card
    $fromLocation = $card->getLocation();
    $card->setLocation($location);
    $card->setTapped(false);
    $card->setRevealed(false);

    $turnCards = Globals::getTurnCards();
    $turnCards[$player->getId()] = ($turnCards[$player->getId()] ?? 0) + 1;
    Globals::setTurnCards($turnCards);

    // if card has boosts (from incorrect passive effect), we remove them

    if ($fromLocation == HAND) {
      $meeples = Meeples::getInLocation('card-' . $card->getId());
      $meepleIds = $meeples->getIds();
      if (!empty($meepleIds)) {
        Meeples::delete($meepleIds);
      }
    } else {
      $meepleIds = [];
    }

    $newState = Cards::getNextPlayedState();
    $newState++;
    $card->setState($newState);

    // notification
    Notifications::playCard($player, $card, $cost, $fromLocation, $location, $meepleIds);

    // When does this happens ????
    if ($location == DISCARD) {
      $deleted = $card->discard();
      Notifications::silentKill($deleted);
    }
    // if played from reserve, it gains fleeting
    elseif ($fromLocation == RESERVE && !in_array(LANDMARK, $card->getSubtypes())) {
      Actions::get(GAIN)->gain($player, $card, FLEETING, 1, null, ['type' => FLEETING]);
    } elseif ($player->getHero()->isAllSpell1Fleeting() && $card->getType() == SPELL && $card->getCostHand() <= 1) {
      Actions::get(GAIN)->gain($player, $card, FLEETING, 1, null, ['type' => FLEETING]);
    }

    if (Globals::getNextCharacterFleeting() == true) {
      Actions::get(GAIN)->gain($player, $card, FLEETING, 1, null, ['type' => FLEETING]);
      Globals::setNextCharacterFleeting(false);
    }

    // should we boost the card
    if (in_array($card->getType(), [CHARACTER, TOKEN]) && Globals::getNextCharacterBoost() > 0) {
      $toBoost = Globals::getNextCharacterBoost();
      $occur = Globals::getNextCharacterBoostOccurence();

      for ($v = 0; $v < $occur - 1; $v++) {
        $this->pushParallelChild(FT::GAIN($card, BOOST, 1));
        $toBoost--;
      }
      if ($toBoost > 0) {
        $this->pushParallelChild(FT::GAIN($card, BOOST, $toBoost));
      }
      Globals::setNextCharacterBoost(0);
      Globals::setNextCharacterBoostOccurence(0);
    }

    // Sound the Howl
    if (
      in_array($card->getType(), [CHARACTER, TOKEN]) &&
      in_array(ANIMAL, $card->getSubtypes()) &&
      Globals::getNextAnimalBoost() > 0
    ) {
      $toBoost = Globals::getNextAnimalBoost();
      $occur = Globals::getNextAnimalBoostOccurence();

      for ($v = 0; $v < $occur - 1; $v++) {
        $this->pushParallelChild(FT::GAIN($card, BOOST, 1));
        $toBoost--;
      }
      if ($toBoost > 0) {
        $this->pushParallelChild(FT::GAIN($card, BOOST, $toBoost));
      }
      Globals::setNextAnimalBoost(0);
      Globals::setNextAnimalBoostOccurence(0);
    }

    if ($fromLocation == RESERVE && $card->getType() == CHARACTER && Globals::getNextReserveCharacterBoost()) {
      $this->pushParallelChild(FT::GAIN($card, BOOST, Globals::getNextReserveCharacterBoost()));
      Globals::setNextReserveCharacterBoost(0);
    }
    // The undergrowth
    $otherLocation = $location == STORM_LEFT ? STORM_RIGHT : ($location == STORM_RIGHT ? STORM_LEFT : 'none');
    if (
      Globals::getNextCharacterBoostV() > 0
      && $card->getType() == CHARACTER &&
      ($player->isInBiome($location, FOREST, true) || ($card->isGigantic() && $player->isInBiome($otherLocation, FOREST, true)))
    ) {
      $toBoost = Globals::getNextCharacterBoostV();
      $occur = Globals::getNextCharacterBoostOccurence();

      for ($v = 0; $v < $occur - 1; $v++) {
        $this->pushParallelChild(FT::GAIN($card, BOOST, 1));
        $toBoost--;
      }
      if ($toBoost > 0) {
        $this->pushParallelChild(FT::GAIN($card, BOOST, $toBoost));
      }
      Globals::setNextCharacterBoostOccurence(0);
      Globals::setNextCharacterBoostV(0);
    }

    // should we anchor the character?
    if (
      Globals::getNextCharacterCost3Anchored() == true &&
      in_array($card->getType(), [CHARACTER, TOKEN]) &&
      $card->getCostHand() <= 3
    ) {
      $this->pushParallelChild(FT::GAIN($card, ANCHORED));
      Globals::setNextCharacterCost3Anchored(false);
    }
    
     // This global is declared as 'obj', so games started before it existed default to [],
    // which must not be interpreted as "the next character is asleep".
    $asleepData = Globals::getNextCharacterAsleep();
    $asleepValue = is_array($asleepData) ? ($asleepData['value'] ?? false) : (bool) $asleepData;
    if ($asleepValue && in_array($card->getType(), [CHARACTER, TOKEN])) {
      $gainNode = FT::GAIN($card, ASLEEP);
      if (is_array($asleepData) && ($asleepData['optional'] ?? false)) {
        $this->pushParallelChild(['type' => NODE_SEQ, 'optional' => true, 'childs' => [$gainNode]]);
      } else {
        $this->pushParallelChild($gainNode);
      }
      Globals::setNextCharacterAsleep(false);
    }

    if (
      Globals::getNextCharacterAnchored() == true &&
      in_array($card->getType(), [CHARACTER, TOKEN])
    ) {
      $this->pushParallelChild(FT::GAIN($card, ANCHORED));
      Globals::setNextCharacterAnchored(false);
    }
   if (
      Globals::getNextCharacterBaseCost3Anchored() == true &&
      in_array($card->getType(), [CHARACTER, TOKEN]) &&
       (($card->getCostHand() <= 3 && in_array($fromLocation, [HAND, LIMBO])) // LIMBO is used for Romantic Encounter / Phoibos, where hand cost shall be used for comparison
       || ($fromLocation == RESERVE && $card->getCostReserve() <= 3)) 
    ) {
      $this->pushParallelChild(FT::GAIN($card, ANCHORED));
      Globals::setNextCharacterBaseCost3Anchored(false);
    }


    if (
      ($card->getType() == CHARACTER && !Players::hasOpponentBlockingPower($player, $location, $card->isGigantic())) ||
      $card->getType() != CHARACTER
    ) {
      $effects = [];

      if ($scout) {
        $effects[] = FT::ACTION(DISCARD, ['cardId' => $card->getId(), 'from' => $location, 'destination' => RESERVE]);
      }

      // insert effect flow
      if ($this->getArg('limited') === true) {
        $effect = $card->getEffectPlayedLimited();
      } else {
        $effect = $card->getEffectPlayed();
      }
      if (!empty($effect)) {
        // Ticket 210043 
        // Multi-{J} effects arrive as a Parallel of siblings. Push each sibling separately so
        // an impossible one (e.g. exhaust with no target) does not block the others, 
        // but can still be selected to voluntarily bypass it. 
        // Also tag untyped merges as PARALLEL for consistency with FlowConvertor.
        if (isset($effect['childs']) && !isset($effect['type'])) {
          $effect['type'] = NODE_PARALLEL;
        }
        if (isset($effect['type']) && $effect['type'] == NODE_PARALLEL) {
          foreach ($effect['childs'] as $t => $child) {
            $effects[] = $child;
          }
        } else {
          $effects[] = $effect;
        }
      }

      if ($effectHand && in_array($fromLocation, [HAND, LIMBO])) {
        $effect = $card->getEffectHand();
        if (!empty($effect)) {
          if (isset($effect['childs']) && !isset($effect['type'])) {
            $effect['type'] = NODE_PARALLEL;
          }
          if (isset($effect['type']) && $effect['type'] == NODE_PARALLEL) {
            foreach ($effect['childs'] as $t => $child) {
              $effects[] = $child;
            }
          } else {
            $effects[] = $effect;
          }
        }
      }

      if ($fromLocation == RESERVE) {
        $effect = $card->getEffectReserve();
        if (!empty($effect)) {
          if (isset($effect['childs']) && !isset($effect['type'])) {
            $effect['type'] = NODE_PARALLEL;
          }
          if (isset($effect['type']) && $effect['type'] == NODE_PARALLEL) {
            foreach ($effect['childs'] as $t => $child) {
              $effects[] = $child;
            }
          } else {
            $effects[] = $effect;
          }
        }
      }

      // if it's a spell, effect are resolved immediately
      if ($card->getType() == SPELL || in_array(SPELL, $card->getAdditionalType())) {

        if ($fromLocation == HAND && Globals::getRemoveFleetingIfSpellPlayedHand() == true) {
          $effects[] = FT::LOOSE($card->getId(), FLEETING);
          Globals::setRemoveFleetingIfSpellPlayedHand(false);
        } elseif (Globals::getRemoveFleetingSpellPlayed() == true) {
          $effects[] = FT::LOOSE($card->getId(), FLEETING);
          Globals::setRemoveFleetingSpellPlayed(false);
        } elseif ((in_array(ARTIST, $card->getSubtypes()) || in_array(SONG, $card->getSubtypes())) && Globals::getRemoveFleetingSongArtistPlayed()) {
          $effects[] = FT::LOOSE($card->getId(), FLEETING);
          Globals::setRemoveFleetingSongArtistPlayed(false);
        }
        if ($card->getType() == SPELL) {
          if (!empty($effects)) {
            $effects = Utils::tagTree(['childs' => $effects], ['sourceId' => $card->getId()]);
            $effects = Utils::tagPId($effects, $card->getPId());
            // foreach ($effects as &$eff['childs']) {
            //   if (isset($eff['pId'])) {
            //     continue;
            //   }
            //   $eff['pId'] = $card->getPId();
            //   if (isset($eff['childs'])) {
            //     foreach ($eff['childs'] as &$child) {
            //       if (isset($child['pId'])) {
            //         continue;
            //       }
            //       $child['pId'] = $card->getPId();
            //     }
            //   }
            // }
            $spellAction = FT::SEQ(FT::PAR($effects), ['action' => SPELL_CLEANUP, 'args' => ['cardId' => $card->getId(), 'event' => [
              'playCard' => true,
              'cardId' => $cardId,
              'cardType' => $card->getType(),
              'from' => $fromLocation,
              'additionalType' => $card->getAdditionalType(),
              'to' => $location,
              'playedFree' => $cost == 0 ? true : false,
              'putAndNotPlayed' => !$effectHand,
              'additionalEffects' => Globals::getAdditionalEffect(),
              'stealOwnership' => $stealOwnership,
            ]], 'pId' => $player->getId()]);
          } else {
            $spellAction = ['action' => SPELL_CLEANUP, 'args' => ['cardId' => $card->getId(), 'event' => [
              'playCard' => true,
              'cardId' => $cardId,
              'cardType' => $card->getType(),
              'additionalType' => $card->getAdditionalType(),
              'from' => $fromLocation,
              'to' => $location,
              'playedFree' => $cost == 0 ? true : false,
              'putAndNotPlayed' => !$effectHand,
              'additionalEffects' => Globals::getAdditionalEffect(),
              'stealOwnership' => $stealOwnership,
            ]], 'pId' => $player->getId()];
          }
          $this->insertAsChild($spellAction);
          $effects = [];
        }
        if (in_array(SPELL, $card->getAdditionalType())) {
          Engine::resolveAction();
        }
      } else {
        // resolving current node as some things are inserted before and after
        Engine::resolveAction();
      }

      if (Globals::getAdditionalEffect() != []) {
        $addEffects = Globals::getAdditionalEffect();

        // Pre-pass: determine if effect signatures have an INFTY limit
        $infiniteEffectSignatures = [];
        foreach ($addEffects as $e) {
            if (($e['limit'] ?? INFTY) == INFTY) {
                $sig = $e['effect'] . '|' . $e['type'] . '|' . $e['from'];
                $infiniteEffectSignatures[$sig] = true;
            }
        }
        
        $processedEffects = [];

        foreach ($addEffects as $i => &$addEffect) {
          if ($addEffect['type'] == $card->getType()) {
            if ($addEffect['from'] == $fromLocation) {
              $effectType = $addEffect['effect'];
              $signature = $effectType . '|' . $addEffect['type'] . '|' . $addEffect['from'] . '|' . ($addEffect['limit'] ?? INFTY);
          
              // If this is a limit=1 effect but an INFTY effect exists for the same group, skip it
              $groupSig = $effectType . '|' . $addEffect['type'] . '|' . $addEffect['from'];
              if (($addEffect['limit'] ?? INFTY) == 1 && isset($infiniteEffectSignatures[$groupSig])) {
                unset($addEffects[$i]);
                continue;
              }
          
              // Skip if we already processed an effect with this signature
              if (in_array($signature, $processedEffects)) {
                unset($addEffects[$i]);
                continue;
              }
          
              $f = 'getEffect' . ucfirst($effectType);
              $newEffect = $card->$f();
          
              if ($newEffect == []) {
                continue;
              }
          
              if (isset($addEffect['to']) && !is_null($addEffect['to'])) {
                if ($addEffect['to'] == 'sourceLocation') {
                  $source = Cards::get($addEffect['sourceId']);
                  if ($location != $source->getLocation()) {
                    continue;
                  }
                } elseif ($addEffect['to'] != $location) {
                  continue;
                }
              }
          
              $matchCount = count(array_filter($addEffects, function ($e) use ($addEffect) {
                return $e['type'] == $addEffect['type']
                  && $e['from'] == $addEffect['from']
                  && $e['effect'] == $addEffect['effect']
                  && ($e['limit'] ?? INFTY) == ($addEffect['limit'] ?? INFTY);
              }));
          
              if (($addEffect['limit'] ?? INFTY) == 1) {
                // Unique cards merge multiple {R} trigrams as NODE_PARALLEL (with type set).
                // Flatten those childs so limit=1 wraps them in XOR (choose one), not run all.
                if (isset($newEffect['childs']) && (!isset($newEffect['type']) || $newEffect['type'] == NODE_PARALLEL)) {
                  $newEffect = $newEffect['childs'];
                } else {
                  $newEffect = [$newEffect];
                }
          
                if ($effectType == RESERVE && $player->getPlayedCards()->filter(function ($c) {
                  return in_array($c->getUid(), ['ALT_CORE_B_BR_30_R', 'ALT_CORE_B_BR_30_C']);
                })->count() > 0) {
                  $newEffect[] = FT::GAIN($card->getId(), BOOST);
                }
          
                // Boost from Fab Lab Unit rare (etc.) must run after the chosen {R}, not as an XOR option
                $afterEffects = [];
                if (($addEffect['boost'] ?? 0) > 0) {
                  $afterEffects[] = FT::GAIN($card->getId(), BOOST, $addEffect['boost']);
                }
          
                if ($matchCount > 1) {
                  $choice = [
                    'type' => NODE_OR,
                    'args' => ['n' => $matchCount],
                    'pId' => $player->getId(),
                    'sourceId' => $addEffect['sourceId'],
                    'childs' => $newEffect
                  ];
                } else {
                  $choice = FT::XOR(...$newEffect);
                }

                $effects[] = empty($afterEffects) ? $choice : FT::SEQ($choice, ...$afterEffects);
              } else {
                if (!empty($newEffect)) {
                  $newEffect = [$newEffect];
                }
          
                if ($effectType == RESERVE && $player->getPlayedCards()->filter(function ($c) {
                  return in_array($c->getUid(), ['ALT_CORE_B_BR_30_R', 'ALT_CORE_B_BR_30_C']);
                })->count() > 0) {
                  $newEffect[] = FT::GAIN($card->getId(), BOOST);
                }
          
                if (empty($newEffect) && ($addEffect['boost'] ?? 0) > 0) {
                  $effects[] = FT::GAIN($card->getId(), BOOST, $addEffect['boost']);
                } else {
                  if (($addEffect['boost'] ?? 0) > 0) {
                    $newEffect[] = FT::GAIN($card->getId(), BOOST, $addEffect['boost']);
                  }
                  $effects[] = FT::SEQ(...$newEffect);
                }
              }
          
              // Mark as processed and remove all matching entries
              $processedEffects[] = $signature;
              foreach ($addEffects as $j => $e) {
                if ($e['type'] == $addEffect['type']
                  && $e['from'] == $addEffect['from']
                  && $e['effect'] == $addEffect['effect']
                  && ($e['limit'] ?? INFTY) == ($addEffect['limit'] ?? INFTY)) {
                  unset($addEffects[$j]);
                }
              }
            }
          }
        }
        Globals::setAdditionalEffect($addEffects);
      }

     $expeditionsBoosts = Globals::getNextCharacterInExpeditionBoost();
      $boostExpedition = $location;
      $oppositeExpedition = $location == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
      if (
        !isset($expeditionsBoosts[$player->getId()][$location]) &&
        $card->isGigantic() &&
        isset($expeditionsBoosts[$player->getId()][$oppositeExpedition])
      ) {
        $boostExpedition = $oppositeExpedition;
      }
      if ($card->getType() == CHARACTER && isset($expeditionsBoosts[$player->getId()][$boostExpedition])) {
        $effects[] = FT::GAIN($card->getId(), BOOST, $expeditionsBoosts[$player->getId()][$boostExpedition]);
        unset($expeditionsBoosts[$player->getId()][$boostExpedition]);
        Globals::setNextCharacterInExpeditionBoost($expeditionsBoosts);
      }

      if (!empty($effects)) {
        // setting a default PId for the effects
        foreach ($effects as &$eff) {
          if (isset($eff['pId'])) {
            continue;
          }
          $eff['pId'] = $card->getPId();
          if (isset($eff['childs'])) {
            foreach ($eff['childs'] as &$child) {
              if (isset($child['pId'])) {
                continue;
              }
              $child['pId'] = $card->getPId();
            }
          }
        }
        $effects = Utils::tagTree(['childs' => $effects], ['sourceId' => $card->getId()]);
        $effects = Utils::updateTree($effects, [0 => 'dioclesLocation'], $card->getLocation(), ['expedition']);
        // Snapshot expedition so delayed ASCEND still works if the source leaves play (e.g. Tengu rush + bounce)
        $effects = Utils::updateTree($effects, 'source', $card->getLocation(), ['expedition']);
        // $effects = Utils::tagTree($effects, ['pId' => $player->getId()]);
        $this->pushAfterFinishingChilds($effects['childs']);
        if ($card->getRarity() == RARITY_UNIQUE) {
          $this->updateAfterFinishingChilds(['noIndependent' => true]);
        }
      }
      // Track if the played card has its own effects that were pushed
      $hasCardEffects = !empty($effects);
    } else {
      $hasCardEffects = false;
      Notifications::message(clienttranslate('Effects are not triggered, due to an effect in the opponent\'s expedition'), []);
    }

    //  #171615: "Use of Sleight of Hand on Scribbling Starfish"
    if ($card->getType() != SPELL) {
      $this->checkImmediateListeners($player, [
        'playCard' => true,
        'cardId' => $cardId,
        'cardType' => $card->getType(),
        'additionalType' => $card->getAdditionalType(),
        'from' => $fromLocation,
        'to' => $location,
        'playedFree' => $cost == 0 ? true : false,
        'putAndNotPlayed' => !$effectHand,
        'additionalEffects' => Globals::getAdditionalEffect(),
        'token' => $card->isToken(),
        'stealOwnership' => $stealOwnership,
      ]);

      // Count children before adding passives
      $afterFinishingNode = Engine::getAfterFinishingNode();
      $childCountBefore = count($afterFinishingNode->getChilds());

      $this->checkAfterListeners($player, [
        'playCard' => true,
        'cardId' => $cardId,
        'cardType' => $card->getType(),
        'additionalType' => $card->getAdditionalType(),
        'from' => $fromLocation,
        'reallyPlayed' => $reallyPlayed,
        'locationPId' => $player->getId(),
        'to' => $location,
        'gigantic' => $card->isGigantic(),
        'playedFree' => $cost == 0 ? true : false,
        'putAndNotPlayed' => !$effectHand,
        'additionalEffects' => Globals::getAdditionalEffect(),
        'token' => $card->isToken(),
        'stealOwnership' => $stealOwnership,
      ]);

      // If the card has its own effects AND passives from other cards were added,
      // force manual choice for effect ordering (per game rules: player chooses order of simultaneous effects)
      $childCountAfter = count($afterFinishingNode->getChilds());
      if ($hasCardEffects && $childCountAfter > $childCountBefore) {
        $this->updateAfterFinishingChilds(['noIndependent' => true]);
      }

      if (in_array($card->getUid(), ['ALT_ALIZE_B_BR_45_C', 'ALT_ALIZE_B_BR_45_R1', 'ALT_ALIZE_B_BR_45_R2'])) {
        $this->checkAfterListeners($player, [
          'playCard' => true,
          'cardId' => $cardId,
          'cardType' => $card->getType(),
          'additionalType' => $card->getAdditionalType(),
          'from' => $fromLocation,
          'reallyPlayed' => $reallyPlayed,
          'locationPId' => $player->getId(),
          'to' => $location,
          'gigantic' => $card->isGigantic(),
          'playedFree' => $cost == 0 ? true : false,
          'putAndNotPlayed' => !$effectHand,
          'additionalEffects' => Globals::getAdditionalEffect(),
          'token' => $card->isToken(),
          'stealOwnership' => $stealOwnership,
        ], true, 'EatMeEnergyBars');
      }
    }
    // throw new \feException(print_r(Globals::getEngine()));

    self::statPlay($cardId);
    $baseStat0 = false;
    foreach ($card->getBiomes() as $biome => $value) {
      if ($value == 0) {
        $baseStat0 = true;
      }
    }

    // Cleanup resolution at end of spell not end of turn
    // if ($card->getType() == SPELL) {
    //   if ($fromLocation == HAND && Globals::getRemoveFleetingIfSpellPlayedHand() == true) {
    //     Engine::insertAtRoot(FT::LOOSE($card->getId(), FLEETING));
    //   } elseif (Globals::getRemoveFleetingSpellPlayed() == true) {
    //     Engine::insertAtRoot(FT::LOOSE($card->getId(), FLEETING));
    //   }

    //   Engine::insertAtRoot(['action' => SPELL_CLEANUP, 'args' => ['cardId' => $card->getId()], 'pId' => $player->getId()]);
    // } else
    if (in_array($card->getType(), [CHARACTER, TOKEN]) && Globals::getRemoveFleetingCharacterPlayed()) {
      Engine::insertAtRoot(FT::LOOSE($card->getId(), FLEETING));
      Globals::setRemoveFleetingCharacterPlayed(false);
    } elseif (in_array($card->getType(), [CHARACTER, TOKEN]) && Globals::getRemoveFleetingCharacterStat0Played() && $baseStat0) {
      Engine::insertAtRoot(FT::LOOSE($card->getId(), FLEETING));
      Globals::setRemoveFleetingCharacterStat0Played(false);
    } elseif ((in_array(ARTIST, $card->getSubtypes()) || in_array(SONG, $card->getSubtypes())) && Globals::getRemoveFleetingSongArtistPlayed()) {
      Engine::insertAtRoot(FT::LOOSE($card->getId(), FLEETING));
      Globals::setRemoveFleetingSongArtistPlayed(false);
    }
  }

  /////////////////////////////
  //  _____     _
  // | ____|___| |__   ___
  // |  _| / __| '_ \ / _ \
  // | |__| (__| | | | (_) |
  // |_____\___|_| |_|\___/
  /////////////////////////////

  public function actSupport($cardId)
  {
    $player = Players::getActive();
    $args = $this->argsChooseAssignment()['_private']['active']['support'];

    if (!in_array($cardId, $args)) {
      throw new \BgaVisibleSystemException('This card cannot be played as support. Should not happen');
    }

    $card = Cards::get($cardId);
    Cards::discard($cardId, 'discard');
    Notifications::supportEffect($player, $card);
    self::statPlay($cardId);
    $abilityActivated = Globals::getAbilityActivatedThisTurn();
    $abilityActivated[$player->getId()] = array_merge(
      $abilityActivated[$player->getId()] ?? [],
      ['discard' => true, 'discardFromHandOrReserve' => true]
    );
    Globals::setAbilityActivatedThisTurn($abilityActivated);
    $abilityActivatedCount = Globals::getAbilityActivatedThisTurnCount();
    $abilityActivatedCount[$player->getId()] = ($abilityActivatedCount[$player->getId()] ?? 0) + 1;
    Globals::setAbilityActivatedThisTurnCount($abilityActivatedCount);
    $abilityActivatedTypeCount = Globals::getAbilityActivatedThisTurnTypeCount();
    $abilityActivatedTypeCount[$player->getId()] = $abilityActivatedTypeCount[$player->getId()] ?? [];
    $abilityActivatedTypeCount[$player->getId()]['discard'] = ($abilityActivatedTypeCount[$player->getId()]['discard'] ?? 0) + 1;
    Globals::setAbilityActivatedThisTurnTypeCount($abilityActivatedTypeCount);

    $effect = $card->getEffectSupport();
    if (!empty($effect)) {
      $effect = Utils::tagTree($effect, ['sourceId' => $card->getId()]);
      // $effect = Utils::tagTree($effect, ['pId' => $player->getId()]);
      $this->insertAsChild($effect);
    }

    $this->checkAfterListeners($player, [
      'cardId' => $cardId,
      'discardCard' => true,
      'playCard' => false,
      'cardType' => $card->getType(),
      'additionalType' => $card->getAdditionalType(),
      'from' => RESERVE,
      'to' => DISCARD_PILE,
      'isSupport' => true,
      'token' => $card->isToken(),
      'sourceId' => $cardId,
      'controller' => $player->getId(),
      'pId' => $player->getId(),
    ], true, 'Discard');
  }

  ////////////////////////
  //  _____
  // |_   _|_ _ _ __
  //   | |/ _` | '_ \
  //   | | (_| | |_) |
  //   |_|\__,_| .__/
  //           |_|
  ////////////////////////
  public function actTap($cardId)
  {
    $player = Players::getActive();
    $args = $this->argsChooseAssignment()['_private']['active']['tap'];

    if (!in_array($cardId, $args)) {
      throw new \BgaVisibleSystemException('This card cannot be tapped. Should not happen');
    }
    $card = Cards::get($cardId);
    $card->setTapped(true);
    Notifications::tapEffect($player, $card);
    $abilityActivated = Globals::getAbilityActivatedThisTurn();
    $abilityActivated[$player->getId()] = array_merge(
      $abilityActivated[$player->getId()] ?? [],
      ['tap' => true]
    );
    Globals::setAbilityActivatedThisTurn($abilityActivated);
    $abilityActivatedCount = Globals::getAbilityActivatedThisTurnCount();
    $abilityActivatedCount[$player->getId()] = ($abilityActivatedCount[$player->getId()] ?? 0) + 1;
    Globals::setAbilityActivatedThisTurnCount($abilityActivatedCount);
    $abilityActivatedTypeCount = Globals::getAbilityActivatedThisTurnTypeCount();
    $abilityActivatedTypeCount[$player->getId()] = $abilityActivatedTypeCount[$player->getId()] ?? [];
    $abilityActivatedTypeCount[$player->getId()]['tap'] = ($abilityActivatedTypeCount[$player->getId()]['tap'] ?? 0) + 1;
    Globals::setAbilityActivatedThisTurnTypeCount($abilityActivatedTypeCount);

    $effect = $card->getEffectTap();
    if (!empty($effect)) {
      $effect = Utils::tagTree($effect, ['sourceId' => $card->getId()]);
      // $effect = Utils::tagTree($effect, ['pId' => $player->getId()]);
      // throw new \feException(print_r($effect));
      $this->insertAsChild($effect);
    }

    $this->checkAfterListeners($player, [
      'cardId' => $card->getId(),
      'cardLocation' => $card->getLocation(),
      'sourceId' => $card->getId(),
    ], true, 'Exhaust');
  }

  ////////////////////////////
  //  ____
  // |  _ \ __ _ ___ ___
  // | |_) / _` / __/ __|
  // |  __/ (_| \__ \__ \
  // |_|   \__,_|___/___/
  ////////////////////////////
  public function actPass()
  {
    $player = Players::getActive();
    $skipped = Globals::getSkippedPlayers();
    if (empty($skipped)) {
      Globals::setFirstPass($player->getId());
    }
    $skipped[] = $player->getId();
    Globals::setSkippedPlayers($skipped);
    Notifications::pass($player);
    $this->checkAfterListeners($player, ['pass'], true, 'EndTurn');
  }
}
