<?php

namespace ALT\Models;

use ALT\Core\Stats;
use ALT\Core\Notifications;
use ALT\Core\Preferences;
use ALT\Managers\Actions;
use ALT\Managers\Cards;
use ALT\Managers\Meeples;
use ALT\Core\Globals;
use ALT\Core\Engine;
use ALT\Helpers\Collection;
use ALT\Helpers\Conditions;
use ALT\Helpers\Utils;
use ALT\Managers\Players;
use ALT\Helpers\FT;

/*
 * Player: all utility functions concerning a player
 */

class Player extends \ALT\Helpers\DB_Model
{
  private $map = null;
  protected $table = 'player';
  protected $primary = 'player_id';
  protected $attributes = [
    'id' => ['player_id', 'int'],
    'no' => ['player_no', 'int'],
    'name' => 'player_name',
    'color' => 'player_color',
    'eliminated' => 'player_eliminated',
    'score' => ['player_score', 'int'],
    'scoreAux' => ['player_score_aux', 'int'],
    'zombie' => 'player_zombie',
    'faction' => 'faction',
  ];
  protected $id;

  public function getUiData($currentPlayerId = null)
  {
    $data = parent::getUiData();
    $current = $this->id == $currentPlayerId;
    $data['deckCount'] = $this->getDeckCount();
    $data['discardCount'] = $this->getDiscardCount();
    $data['mana'] = $this->getMana();
    $data['totalMana'] = $this->getTotalMana();
    $data['handCount'] = $this->getHand()->count();
    $data['hand'] = $current ? $this->getHand()->ui() : [];
    $data['manaCards'] = $current ? $this->getManaCards() : [];
    $data['reserveSlots'] = $this->getReserveSlots();
    $data['landmarkSlots'] = $this->getLandmarkSlots();
    return $data;
  }

  public function getDeckCount()
  {
    return Cards::countInLocation("deck-$this->id") + Cards::countInLocation("reveal-$this->id");
  }
  
  public function getDiscardCount()
  {
    return Cards::getFiltered($this->id, DISCARD_PILE)->count();
  }
  
  public function hasDeckCards()
  {
    return $this->getDeckCount() > 0;
  }

  public function getHero()
  {
    $pId = $this->id;
    return Cards::getInLocation("board-hero-$pId")->first();
  }

  public function getHeroCollection()
  {
    $pId = $this->id;
    return Cards::getInLocation("board-hero-$pId");
  }

  public function getPref($prefId)
  {
    return Preferences::get($this->id, $prefId);
  }

  public function getStat($name)
  {
    $name = 'get' . \ucfirst($name);
    return Stats::$name($this->id);
  }

  public function canTakeAction($action, $ctx)
  {
    return Actions::isDoable($action, $ctx, $this);
  }

  public function draw(
    $nb,
    $fromLocation = null,
    $toLocation = null,
    $source = null,
    $publicMsg = null,
    $privateMsg = null,
    $tapped = false,
    $exhaustingPlayer = null
  ) {
    $fromLocation = $fromLocation ?? 'deck-' . $this->id;
    $toLocation = $toLocation ?? 'hand';
    $public = $toLocation == 'hand' ? false : true;
    $cards = new Collection();
    if (is_null($exhaustingPlayer)) {
      $exhaustingPlayer = Players::getActiveId();
    }

    // we check if we have a revealed card
    if ($fromLocation == 'deck-' . $this->id && Cards::countInLocation("reveal-$this->id") > 0) {
      // we reduce $nb as there is already a card that has been revealed
      $nb--;
      $cards = Cards::getInLocation("reveal-$this->id");
      foreach ($cards as $ccId => $cc) {
        $cc->setLocation($toLocation);
      }
      Notifications::silentKill([], $cards->getIds());
      $cards = Cards::getMany($cards->getIds());
    }

    if ($nb > 0) {
      $cards = Cards::pickForLocation($nb, $fromLocation, $toLocation)->merge($cards);
    }

    if ($tapped == true) {
      foreach ($cards as $cId => $card) {
        if ($toLocation == MANA) {
          $card->setTapped(true);
        } else {
          Engine::insertAsChild(FT::ACTION(EXHAUST, ['cardId' => $cId], ['pId' => $exhaustingPlayer, 'optional' => false, 'sourceId' => $source->getId()]));
        }
      }
    }

    if ($cards->count() == 0) {
      return $cards;
    }

    if ($toLocation == MANA) {
      Notifications::discardMana($this, $cards, $privateMsg, $publicMsg, ['card2' => $source, 'fromLocation' => $fromLocation]);
    } elseif ($source !== null) {
      Notifications::drawCards(
        $this,
        $cards,
        $privateMsg ?? clienttranslate('You draw ${card_names} from your deck (${card_name2}\'s effect)'),
        $publicMsg ??
          ($public
            ? clienttranslate('${player_name} draws ${card_names} from its deck (${card_name2}\'s effect)')
            : clienttranslate('${player_name} draws ${n} card(s) from its deck (${card_name2}\'s effect)')),
        ['card2' => $source],
        $public
      );
    } else {
      Notifications::drawCards($this, $cards, null, null, [], $public);
    }
    return $cards;
  }

  ////////////////////////////////////////
  //  ____       _   _
  // / ___|  ___| |_| |_ ___ _ __ ___
  // \___ \ / _ \ __| __/ _ \ '__/ __|
  //  ___) |  __/ |_| ||  __/ |  \__ \
  // |____/ \___|\__|\__\___|_|  |___/
  ////////////////////////////////////////

  public function payMana($n)
  {
    $cards = $this->getManaCards(false)->limit($n);
    if ($cards->count() < $n) {
      throw new \BgaUserException(clienttranslate('You don\'t have enough mana to pay'));
    }

    foreach ($cards as $card) {
      $card->setTapped(true);
    }
  }

  ///////////////////////////////////////////////////
  //    ____              _
  //   / ___|__ _ _ __ __| |___
  //   | |   / _` | '__/ _` / __|
  //   | |__| (_| | | | (_| \__ \
  //   \____\__,_|_|  \__,_|___/
  ///////////////////////////////////////////////////
  public function getHand($type = null)
  {
    return Cards::getHand($this->id)->filter(function ($card) use ($type) {
      return is_null($type) || $card->getType() == $type || in_array($type, $card->getAdditionalType());
    });
  }
  
  // public function getManaChoice()
  // {
  //   return Cards::getManaChoice($this->id);
  // }

  public function getPlayedCards($type = null)
  {
    return Cards::getPlayedCards($this->id, $type);
  }

  public function getInfinityCards($type = null)
  {
    return Cards::getReserveCards($this->id, $type)->filter(function ($c) {
      return !empty($c->getEffectInfinity());
    });
  }

  public function countCardsInLocation($location, $types)
  {
    return $this->getPlayedCards()->filter(function ($c) use ($location, $types) {
      return $c->getLocation() == $location && (in_array($c->getType(), $types) || count(array_intersect($types, $c->getAdditionalType())) > 0);
    })->count();
  }

  public function hasPlayedCard($id)
  {
    return Cards::hasPlayedCard($this->id, $id);
  }

  public function getReserveCards()
  {
    return Cards::getReserveCards($this->id);
  }

  public function getAllReserveSlots()
  {
    $reserve = 0;
    foreach ($this->getReserveCards() as $cId => $c) {
      if (!$c->isTapped()) {
        $reserve += $c->getAllReserveSlots();
      }
    }
    return $reserve;
  }

  public function getReserveSlots()
  {
    if (is_null($this->getHero())) {
      return 0;
    }
    $reserve =  $this->getHero()->getReserveSlots();
    foreach ($this->getPlayedCards() as $cId => $c) {
      $reserve += $c->getReserveSlots();
    }
    foreach ($this->getReserveCards() as $cId => $c) {
      if (!$c->isTapped() && $c->getIgnoreReserveLimit() == true) {
        $reserve++;
      }
    }
    $reserve += Players::getAllReserveSlots();
    return max(0, $reserve);
  }

  public function getLandmarkSlots()
  {
    if (is_null($this->getHero())) {
      return 0;
    }
    $n = (int) $this->getHero()->getLandmarkSlots();
    foreach ($this->getLandmarks() as $card) {
      $completed = $card->getEffectCompleted();
      if (!is_array($completed)) {
        continue;
      }
      if (
        !in_array(FEAT, $card->getSubtypes())
        && !array_key_exists('landmarkSlots', $completed)
      ) {
        continue;
      }
      if (Meeples::countMeeples('card-' . $card->getId(), FEAT_COMPLETED) < 1) {
        continue;
      }
      if (isset($completed['landmarkSlots'])) {
        $n = (int) $completed['landmarkSlots'];
      }
    }
    return $n;
  }

  public function getPermanents()
  {
    return Cards::getPlayedCards($this->id, PERMANENT);
  }

  public function getLandmarks()
  {
    return Cards::getPlayedCards($this->id, PERMANENT)->where('subtypes', LANDMARK);
  }

  public function getFeats()
  {
    return Cards::getPlayedCards($this->id, PERMANENT)->where('subtypes', FEAT);
  }
  
  /**
   * Number of completed Feats among this player's Landmark permanents (see FEAT_COMPLETED meeple).
   */
  public function getCompletedFeat()
  {
    $n = 0;
    foreach ($this->getLandmarks() as $card) {
      $n += Meeples::countMeeples('card-' . $card->getId(), FEAT_COMPLETED);
    }
    return $n;
  }

  /**
   * Sum of reserve-character Tough granted by completed Feat effects.
   */
  public function getCompletedFeatReserveCharacterTough()
  {
    $n = 0;
    foreach ($this->getLandmarks() as $card) {
      $completed = $card->getEffectCompleted();
      if (!is_array($completed)) {
        continue;
      }
      if (
        !in_array(FEAT, $card->getSubtypes())
        && !array_key_exists('reserveCharacterTough', $completed)
      ) {
        continue;
      }
      if (Meeples::countMeeples('card-' . $card->getId(), FEAT_COMPLETED) < 1) {
        continue;
      }
      $n += (int) ($completed['reserveCharacterTough'] ?? 0);
    }
    return max(0, $n);
  }

  public function getManaCards($tapped = null)
  {
    return Cards::getFiltered($this->id, MANA)->filter(function ($card) use ($tapped) {
      return is_null($tapped) || ($tapped === true && $card->isTapped()) || ($tapped === false && !$card->isTapped());
    });
  }

  public function getMana()
  {
    return $this->getManaCards(false)->count();
  }

  public function getTotalMana()
  {
    return $this->getManaCards()->count();
  }

  public function getStormToken($type)
  {
    return Meeples::getStormTokens($this->id)
      ->filter(function ($m) use ($type) {
        return $m->getType() == $type;
      })
      ->first();
  }

  public function getCompanionToken()
  {
    return $this->getStormToken(COMPANION);
  }

  public function getHeroToken()
  {
    return $this->getStormToken(HERO);
  }

  public function isAscended($expedition)
  {
    $tokens = Meeples::getAscended($this->id, $expedition);
    if (count($tokens) == 0) {
      return false;
    }
    return true;
  }

  public function getDeck($deckNumber)
  {
    return Cards::getFiltered($this->id, 'deck-' . $deckNumber)->merge(
      Cards::getFiltered($this->id, 'board-hero-' . $deckNumber)
    );
  }

  public function getAddRoll()
  {
    $add = 0;
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      $add += $card->getAddRoll();
    }
    return $add;
  }

  public function getAddDice()
  {
    $add = 0;
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      $add += $card->getAddDice();
    }
    return $add;
  }

  public function getReserveAdd()
  {
    $add = 0;
    foreach ($this->getPlayedCards() as $cId => $card) {
      $add += $card->getReserveAdd();
    }
    return $add;
  }

  public function getResupply2()
  {
    foreach ($this->getPlayedCards() as $cId => $card) {
      if ($card->getResupply2()) {
        return true;
      }
    }
    return false;
  }

  public function getReduceCostType($playedCard)
  {
    $reduction = 0;
    $minimumFloor = 0;
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      if (!empty($card->getReduceCostType())) {
        $type = $card->getReduceCostType();
        foreach ($type as $playedType => $info) {
          if ($playedType == $playedCard->getType() || in_array($playedType, $playedCard->getAdditionalType())) {
            if (isset($info['maxHandCost']) && $playedCard->getCostHand() <= $info['maxHandCost']) {
              $reduction += $info['reduction'];
              if (isset($info['minimum'])) {
                $minimumFloor = max($minimumFloor, $info['minimum']);
              }
            } elseif (isset($info['minHandCost']) && $playedCard->getCostHand() >= $info['minHandCost']) {
              $reduction += $info['reduction'];
              if (isset($info['minimum'])) {
                $minimumFloor = max($minimumFloor, $info['minimum']);
              }
            } elseif (isset($info['minBaseCost'])) {
              $baseCost = $info['minBaseCost'];
              // Studious Acolyte
              if ($playedCard->getLocation() == RESERVE && $playedCard->getCostReserve() >= $baseCost) {
                $reduction += $info['reduction'];
                if (isset($info['minimum'])) {
                  $minimumFloor = max($minimumFloor, $info['minimum']);
                }
              } elseif ($playedCard->getLocation() == HAND && $playedCard->getCostHand() >= $baseCost) {
                $reduction += $info['reduction'];
                if (isset($info['minimum'])) {
                  $minimumFloor = max($minimumFloor, $info['minimum']);
                }
              }
            }
          }
        }
      }
    }
    return ['reduction' => $reduction, 'minimum' => $minimumFloor];
  }

  public function getExhaustedReserveSlots()
  {
    $slots = 0;
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      $slots += $card->getExhaustedReserveSlots();
    }
    return $slots;
  }

  public function getMinimumReserveCost()
  {
    $cost = 0;
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      $cost += $card->getMinimumReserveCost();
    }
    return $cost;
  }

  public function getRegionDifference()
  {
    if (is_null($this->getCompanionToken())) {
      return 7;
    }
    $companionPos = explode('-', $this->getCompanionToken()->getLocation())[1];
    $heroPos = explode('-', $this->getHeroToken()->getLocation())[1];
    return max($companionPos - $heroPos, 0);
  }

  public function checkVictory()
  {
    if (is_null($this->getCompanionToken())) {
      return 7;
    }
    $companionPos = explode('-', $this->getCompanionToken()->getLocation())[1];
    $heroPos = explode('-', $this->getHeroToken()->getLocation())[1];
    return [$companionPos - $heroPos <= 0, $heroPos + (7 - $companionPos)];
  }

  public function getBiomeInStorms()
  {
    $tokens = Meeples::getStormTokens($this->id);
    $locations = [];
    $storms = Globals::getStorm();

    if (Globals::isTieBreakerMode()) {
      // In tie-break each expedition is in all biomes
      return [HERO => [FOREST, MOUNTAIN, OCEAN], COMPANION => [FOREST, MOUNTAIN, OCEAN]];
    }

    foreach ($tokens as $i => $token) {
      $sId = $token->getLocationArg();

      // check if there is a terrain marker
      $markers = Meeples::getOfType('storm-' . $sId, [OCEAN, FOREST, MOUNTAIN]);
      if ($markers->count() > 0) {
        foreach ($markers as $mId => $marker) {
          $locations[$token->getType()] = [$marker->getType()];
        }
        continue;
      }


      if ($sId == 0 || $sId == 7) {
        $locations[$token->getType()] = [FOREST, MOUNTAIN, OCEAN];
        continue;
      }

      $card = $storms[intdiv($sId + 1, 2)];
      $storm = STORM_CARDS[$card['cardId']];

      if ($card['rotated']) {
        $storm = array_reverse($storm);
      }
      $sId--;

      $locations[$token->getType()] = $storm[$sId % 2];
    }

    return $locations;
  }

  public function isInBiome($storm, $biome, $excludeMove = false)
  {
    return in_array($biome, self::getBiomes($storm, $excludeMove));
  }

  public function getBiomes($storm, $excludeMove = false)
  {

    $biomes = $this->getBiomeInStorms();
    if ($storm == '') {
      return false;
    }

    $expedition = $storm == STORM_LEFT ? HERO : COMPANION;
    $newBiomes = [];
    foreach ($biomes[$expedition] as $b) {
      $newBiomes[$b] = $b;
    }

    Players::biomesModifier($newBiomes, $this, $storm, false, $excludeMove);
    return $newBiomes;
  }

  public function advanceStorm($token, $biomes, $n = 1, $hasMovedFromAscension = false, $notify = true, $source = null)
  {
    $getToken = 'get' . ucfirst($token) . 'Token';
    $tokenMeeple = $this->$getToken();
    // TODO: manage immobile
    $location = $tokenMeeple->getLocationArg();
    $expedition = $token == HERO ? STORM_LEFT : STORM_RIGHT;
    $isAscended = $this->isAscended($expedition);


    // if hero we increase
    $delta = $token == HERO ? $n : $n * -1;
    $sId = $token == HERO ? min(max(0, $location + $delta), 7) : max(0, min(7, $location + $delta));

    if ($sId == $location) {
      Notifications::message(clienttranslate('${player_name} expedition cannot move'), ['player' => $this]);
      return false;
    }

    // Set new location
    $tokenMeeple->setLocation('storm-' . $sId);

    // needed to effect after moving
    $moves = Globals::getStormMoves();
    $moves[$this->id][$expedition] = [
      'biomes' => is_array($biomes) ? $biomes : [],
      'moves' => $n,
      'ascended' => $isAscended,
      'hasMovedFromAscension' => $hasMovedFromAscension
    ];
    Globals::setStormMoves($moves);

    // Do we need to reveal storm?
    $revealed = null;
    $storms = Globals::getStorm();
    $stormIndex = intdiv($sId + 1, 2);
    if (!$storms[$stormIndex]['visible']) {
      $storms[$stormIndex]['visible'] = true;
      $revealed = $storms[$stormIndex];
      Globals::setStorm($storms);
    }

    if ($notify) {
      Notifications::moveStormToken($this, $biomes, $tokenMeeple, $stormIndex, $revealed, $source, $hasMovedFromAscension);
    }
    return true;
  }

  public function nightCleanup()
  {
    $deletedCards = new Collection();
    $deletedCardTokens = new Collection();
    $deletedMeepleIds = [];
    $cleanupCards = [];
    $movedToReserve = [];
    $eternals = [];
    $seasoned = [];
    $gigantic = [];

    foreach ($this->getPlayedCards() as $cId2 => $card2) {
      if ($card2->isEternal()) {
        $eternals[] = $cId2;
      }
      if ($card2->isSeasoned()) {
        $seasoned[] = $cId2;
      }
      if ($card2->isGigantic()) {
        $gigantic[] = $cId2;
      }
    }

    foreach ($this->getPlayedCards()->sortBy('type') as $cId => $card) {
      $nodes = [];
      if (in_array(LANDMARK, $card->getSubtypes())) {
        continue;
      }

      // Jinn's effect (ask question to put in mana instead of discarding)
      if (
        !$card->hasToken(ASLEEP) && !$card->hasToken(ANCHORED) &&
        ($card->isLeaveExpeditionToMana() // Mighty Jinn
          || $card->isLeaveExpeditionToManaOrDraw() // Mighty Jinn rare
          || ($card->isLeaveExpeditionBoostedToMana() && $card->countToken(BOOST) > 0) // Tiny Jinn
        )
      ) {
        $nodes[] = FT::ACTION(DISCARD, [
          'cardId' => $cId,
          'destination' => MANA,
          'tapped' => true,
          'force' => true,
        ], ['pId' => $card->getPId()]);
        $newNode = FT::ACTION(DISCARD, [
          'cardId' => $cId,
          'destination' => RESERVE,
          'force' => true,
          'seasoned' => in_array($cId, $seasoned)
        ], ['pId' => $card->getPId()]);

        if ($card->isLeaveExpeditionToManaOrDraw()) {
          $newNode = FT::SEQ($newNode, FT::ACTION(DRAW, ['players' => ME], ['pId' => $card->getPId()]));
        }
        $nodes[] = $newNode;

        $toAdd = FT::XOR(...$nodes);
        $toAdd['pId'] = $card->getPId();
        $toAdd['sourceId'] = $cId;
        Engine::pushAfterFinishingChilds([$toAdd]);
        continue;
      }

      if (!$card->hasToken(ASLEEP) && !$card->hasToken(ANCHORED) && !$card->hasToken(FLEETING) && $card->isLeaveExpeditionDefect()) {
        $toAdd = FT::SEQ(
          FT::GAIN($cId, FLEETING),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect', 'cardId' => $cId], ['sourceId' => $cId])
        );
        $toAdd['pId'] = $card->getPId();
        $toAdd['sourceId'] = $cId;
        Engine::pushAfterFinishingChilds([$toAdd]);
        continue;
      }


      // Expedition permanent, if the player hasn't moved, it stays
      if (in_array(EXPEDITION, $card->getSubtypes())) {
        $hasMoved = false;
        $moves = Globals::getStormMoves();
        $expeditionMoves = Globals::getExpeditionMoves();
        if (isset($moves[$this->id]) && isset($moves[$this->id][$card->getLocation()]) && $moves[$this->id][$card->getLocation()]['moves'] > 0) {
          $hasMoved = true;
        }

        if (isset($expeditionMoves[$this->id]) && isset($expeditionMoves[$this->id][$card->getLocation()]) && $expeditionMoves[$this->id][$card->getLocation()] > 0) {
          $hasMoved = true;
        }
        if (!$hasMoved) {
          continue;
        }
      }

      // Remove card if Fleeting but is not anchored
      if ($card->hasToken(FLEETING) && !$card->hasToken(ANCHORED) && !$card->hasToken(ASLEEP) && !in_array($cId, $eternals)) {
        $originalLocation = $card->getLocation();
        $deletedMeepleIds = array_merge($deletedMeepleIds, $card->discard($seasoned, $gigantic));

        if ($card->isToken()) {
          // delete the card as it's a token
          $deletedCardTokens[] = $card;
          Cards::delete($cId);
        } else {
          Actions::get(DISCARD)->checkAfterListeners($this, [
            'discardCard' => true,
            'cardsToListen' => [], // we add the discarded cards as they should react even if not played
            'cardId' => $cId,
            'token' => false,
            'from' => $originalLocation,
            'to' => DISCARD_PILE,
            'sacrifice' => false,
          ]);
          $deletedCards[$cId] = $card;
        }
        continue;
      }



      // Move card without anchored,asleep to reserve
      if (!$card->hasToken(ANCHORED) && !$card->hasToken(ASLEEP) && !in_array($cId, $eternals)) {
        // move card to reserve
        $deletedMeepleIds = array_merge($deletedMeepleIds, $card->moveToReserve($seasoned, $gigantic));
        if ($card->isToken()) {
          // delete the card as it's a token
          $deletedCardTokens[] = $card;
          Cards::delete($cId);
        } else {
          $movedToReserve[] = $cId;
        }
        continue;
      }

      // Remove Anchored / Asleep tokens
      $deletedMeepleIds = array_merge($deletedMeepleIds, $card->nightCleanup());
      $cleanupCards[] = $cId;
    }

    Notifications::nightCleanup($this, $deletedCards, $deletedMeepleIds, Cards::getMany($movedToReserve), $deletedCardTokens);
    if (!empty($cleanupCards)) {
      Notifications::cleanupCards($this, $cleanupCards);
    }

    return array_merge($deletedCards->getIds(), $movedToReserve);
  }

  /************** Expedition calculation *******/
  public function getBiomeStrength($expeditions = STORMS, $includeModifiers = true)
  {
    $strengths = [];
    $cards = $this->getPlayedCards();
    $validBiomes = [FOREST => 0, OCEAN => 0, MOUNTAIN => 0];

    if (Globals::isTieBreakerMode()) {
      Players::biomesModifier($validBiomes, $this, '', true);
    }

    foreach ($expeditions as $i => $exp) {
      $strength = [OCEAN => 0, MOUNTAIN => 0, FOREST => 0]; // OCEAN/MOUNTAIN/FOREST
      $increaseBiomesToHighest = $this->hasIncreaseBiomesHighest($exp);
      list($excludeIncreaseStats, $allIncrease) = $this->hasIncreaseAllOtherCharactersBiomesHighest();
      $otherExpedition = $exp == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
      foreach ($cards as $c => $card) {
        if ($card->getLocation() != $exp && !$card->hasToken(GIGANTIC) && !$card->isGigantic()) {
          continue;
        }
        $giganticIncrease = false;

        if ($card->hasToken(ASLEEP)) {
          continue;
        }
        if ($card->isGigantic() && $this->hasIncreaseBiomesHighest($otherExpedition)) {
          $giganticIncrease = true;
        }

        $increase = $increaseBiomesToHighest || $giganticIncrease || $allIncrease;
        if ($excludeIncreaseStats == $c) {
          $increase = $increaseBiomesToHighest || $giganticIncrease;
        }
        $biome = $card->getBiomes($includeModifiers, $increase);

        foreach ($biome as $bi => $value) {
          $strength[$bi] += $value;
        }
      }
      foreach ($strength as $biome => $s) {
        if (!isset($validBiomes[$biome])) {
          $strength[$biome] = 0;
        }
      }
      $strengths[$exp] = $strength;
    }



    return $strengths;
  }

  public function hasBlockingPower($expedition, $isGigantic = false)
  {
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      // if there are eat me energy bars
      if ($card->getLocation() != $expedition && !$card->isGigantic() && !$isGigantic) {
        continue;
      }

      if ($card->isBlockingPower()) {
        return true;
      }
    }
    return false;
  }

  public function hasGigantic()
  {
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      if ($card->isGigantic()) {
        return true;
      }
    }
    return false;
  }

  public function hasExpeditionSeasoned($expedition = null)
  {
    foreach ($this->getPlayedCards() as $cId => $card) {
      if (!is_null($expedition) && $card->getLocation() != $expedition) {
        continue;
      }
      if ($card->isExpeditionSeasoned()) {
        return true;
      }
    }
    return false;
  }

  public function hasExpeditionToughBoosted($expedition = null)
  {
    foreach ($this->getPlayedCards() as $cId => $card) {
      if (!is_null($expedition) && $card->getLocation() != $expedition) {
        continue;
      }
      if ($card->getExpeditionTough() == 'boosted') {
        return true;
      }
    }
    return false;
  }

  public function hasIncreaseBiomesHighest($expedition)
  {
    foreach ($this->getPlayedCards()->where('location', $expedition) as $cId => $card) {
      if ($card->isIncreaseBiomesHighest()) {
        return true;
      }
    }

    return false;
  }

  public function hasIncreaseAllOtherCharactersBiomesHighest()
  {
    $excludeList = null;
    $found = false;
    foreach ($this->getPlayedCards() as $cId => $card) {
      if ($card->countToken(BOOST) > 0 && $card->isIncreaseAllOtherCharactersBiomesHighest()) {
        if (is_null($excludeList)) {
          $excludeList = $cId;
          $found = true;
        } else {
          $excludeList = null;
          return [$excludeList, $found];
        }
      }
    }
    return [$excludeList, $found];
  }

  public function hasAdvanceTwiceDusk($expedition)
  {
    $advance = 0;
    foreach ($this->getPlayedCards()->where('location', $expedition) as $cId => $card) {
      if ($card->isAdvanceTwiceDusk()) {
        $advance++;
      }
    }
    return $advance;
  }

  public function hasResupplyIfAscended()
  {
    $advance = 0;
    foreach ($this->getPlayedCards() as $cId => $card) {
      if ($card->isResupplyIfAscended()) {
        return true;
      }
    }
    return false;
  }

  public function hasBoostIfAscended()
  {
    foreach ($this->getPlayedCards() as $cId => $card) {
      if ($card->isBoostIfAscended()) {
        return true;
      }
    }
    return false;
  }

  public function hasOppositeDefender($expedition)
  {
    foreach ($this->getPlayedCards()->where('location', $expedition) as $cId => $card) {
      if ($card->isOppositeDefender()) {
        return true;
      }
    }
    return false;
  }

  public function hasBlockMoveExpedition($expedition)
  {
    // A Gigantic character faces both Expeditions, so it must block movement on
    // either side regardless of the Expedition slot it is physically located in
    // (mirrors the dusk logic in Players::biomesModifier).
    foreach ($this->getPlayedCards() as $cId => $card) {
      if ($card->getLocation() != $expedition && !$card->isGigantic()) {
        continue;
      }
      if ($card->isBlockMoveExpedition()) {
        return true;
      }
    }
    return false;
  }

  public function hasBlockOpponentReserveGain()
  {
    foreach ($this->getPlayedCards() as $cId => $card) {
      if ($card->isBlockOpponentReserveGain()) {
        return true;
      }
    }
    return false;
  }

  public function hasBlockGainNewCounters()
  {
    foreach ($this->getPlayedCards() as $cId => $card) {
      if ($card->isBlockGainNewCounters()) {
        return true;
      }
    }
    return false;
  }

  public function hasProtectAnchoredInExpedition($expedition, $gigantic = false)
  {
    $otherExpedition = $expedition == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;

    foreach ($this->getPlayedCards()->where('location', $expedition) as $cId => $card) {
      if ($card->isProtectAnchoredInExpedition()) {
        return true;
      }
    }
    // #169707: "Floral Tent did not protect Boosted Kaibara"
    if ($gigantic) {
      foreach ($this->getPlayedCards()->where('location', $otherExpedition) as $cId => $card) {
        if ($card->isProtectAnchoredInExpedition()) {
          return true;
        }
      }
    }
    return false;
  }

  public function hasProtectBoostedInExpedition($expedition, $gigantic = false)
  {
    $otherExpedition = $expedition == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
    foreach ($this->getPlayedCards()->where('location', $expedition) as $cId => $card) {
      if ($card->isProtectBoostedInExpedition()) {
        return true;
      }
    }

    // #169707: "Floral Tent did not protect Boosted Kaibara"
    if ($gigantic) {
      foreach ($this->getPlayedCards()->where('location', $otherExpedition) as $cId => $card) {
        if ($card->isProtectBoostedInExpedition()) {
          return true;
        }
      }
    }

    return false;
  }


  public function canPlayTappedCards($type = null, $location = null, $additionalType = [])
  {
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      $playTap = $card->getPlayTappedCards();
      $playTappedCharacters = $card->getPlayTappedCharacters();
      $playAllTapped = $card->getPlayTappedAllCards();

      if (!is_bool($playAllTapped) && !is_array($playAllTapped)) {
        if (Utils::checkAttributeCondition('tough', $playAllTapped, $this, $card) == "1") {
          $playAllTapped = true;
        } else {
          $playAllTapped = false;
        }
      }

      if (!is_bool($playTappedCharacters) && !is_array($playTappedCharacters)) {
        if (Utils::checkAttributeCondition('tough', $playTappedCharacters, $this, $card) == "1") {
          $playTappedCharacters = true;
        } else {
          $playTappedCharacters = false;
        }
      }

      if (!$playTappedCharacters && !$playAllTapped && (is_null($playTap) || empty($playTap))) {
        continue;
      }
      // for all cards
      if ((isset($playTap['type']) && $playTap['type'] == 'all') || $playAllTapped) {
        return true;
      }
      // location check
      if (isset($playTap['location']) && !is_null($location)) {
        if ($location != $card->getLocation()) {
          continue;
        }
      }

      if ($playTappedCharacters && $type == CHARACTER) {
        return true;
      }

      if (!is_null($type) && isset($playTap['type']) && ($playTap['type'] == $type || in_array($playTap['type'], $additionalType))) {
        return true;
      }
    }
    return false;
  }

  public function getOpponentAdditionalCost($type)
  {
    $f = 'getIncreaseOpponent' . ucfirst($type) . 'Cost';
    $cost = 0;
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      $penalty = $card->$f();
      if (is_int($penalty)) {
        $cost += $card->$f();
      } else {
        if (!is_null(Utils::checkAttributeCondition('tough', $penalty, $this, $card))) {
          $cost += (int) explode(':', $card->$f())[0];
        }
      }

      $penalties =  $card->getIncreaseOpponentCardsCost();
      if (!is_array($penalties)) {
        $penalties = [$penalties];
      }
      foreach ($penalties as $penalty) {
        if (is_int($penalty)) {
          $cost += $penalty;
        } else {
          if (!is_null(Utils::checkAttributeCondition('tough', $penalty, $this, $card))) {
            $cost += (int) explode(':', $penalty)[0];
          }
        }
      }
    }
    return $cost;
  }

  public function getOpponentMinimumCost($type)
  {
    $cost = 0;
    foreach ($this->getPlayedCards()->merge($this->getInfinityCards()) as $cId => $card) {
      if ($type == CHARACTER) {
        $penalty = $card->getOpponentCharactersMinimumCost();
        if (is_int($penalty)) {
          $cost = max($cost, $card->getOpponentCharactersMinimumCost());
        } else {
          if (!is_null(Utils::checkAttributeCondition('tough', $penalty, $this, $card))) {
            $cost = max($cost, (int) explode(':', $card->getOpponentCharactersMinimumCost())[0]);
          }
        }
      }

      $penalties =  $card->getOpponentCardsMinimumCost();
      if (!is_array($penalties)) {
        $penalties = [$penalties];
      }
      foreach ($penalties as $penalty) {
        if (is_int($penalty)) {
          $cost = max($cost, $penalty);
        } else {
          if (!is_null(Utils::checkAttributeCondition('tough', $penalty, $this, $card))) {
            $cost = max($cost, (int) explode(':', $penalty)[0]);
          }
        }
      }
    }
    return $cost;
  }

  /********* Deck setup ***********/
  public function initializeDecks()
  {
    $i = 0;
    $decks = [];
    // call the API to get the various cards/decks

    // Add the preconstructed decks last
    // if deck format is DEMO, setup the demo decks
    if (Globals::getDeckFormat() == 'DEMO') {
      $decks = array_merge($decks, Cards::setupDemoDeck($this, $i, $decks));
    } else {
      $decks = array_merge($decks, Cards::setupPrecoDeck($this, $i, $decks));
    }
    $allDecks = Globals::getPlayerDecks();
    $allDecks[$this->id] = $decks;
    Globals::setPlayerDecks($allDecks);
  }
 
  /**
   * Counts permanents/characters whose {@see getDynamicTough()} grants universal +1/+2 Tough to other Characters.
   * When {@see $receiver} is set (typically the card whose {@see Card::getTough()} is being computed), sources with
   * {@see effectCompleted} <code>universalToughScope</code> only apply to qualifying receivers (e.g. Gather the Pack).
   * Each qualifying source stacks (two completed Gather the Pack → Tough 2 on expedition characters).
   */
  public function countUniversalCharacterTough(?Card $receiver = null)
  {
    $tier2 = count(
      $this->getPlayedCards()->filter(function ($card) use ($receiver) {
        return $this->playedCardGrantsUniversalCharacterTough($card, 2, $receiver);
      })
    );

    $tier1 = count(
      $this->getPlayedCards()->filter(function ($card) use ($receiver) {
        return $this->playedCardGrantsUniversalCharacterTough($card, 1, $receiver);
      })
    );

    return $tier2 * 2 + $tier1;
  }

  /** Completed Gather the Pack feats use {@see effectCompleted} <code>universalToughScope</code> (expedition / expeditionAnchored). */
  private function isGatherPackUniversalToughSource(Card $source): bool
  {
    $completed = $source->getEffectCompleted();
    if (!is_array($completed)) {
      return false;
    }
    $scope = $completed['universalToughScope'] ?? null;
    return $scope === 'expedition' || $scope === 'expeditionAnchored';
  }

  /**
   * Whether $source counts as a universalCharacter1/2 tough source for $receiver (see {@see countUniversalCharacterTough}).
   */
  private function playedCardGrantsUniversalCharacterTough(Card $source, int $tier, ?Card $receiver): bool
  {
    $want = $tier === 2 ? 'universalCharacter2' : 'universalCharacter1';
    $dt = $source->getDynamicTough();
    $matches = false;
    if (!is_array($dt)) {
      $matches = Utils::checkAttributeCondition('tough', $dt, $this, $source) == $want;
    } else {
      foreach ($dt as $singleTough) {
        if (Utils::checkAttributeCondition('tough', $singleTough, $this, $source) == $want) {
          $matches = true;
          break;
        }
      }
    }
    if (!$matches) {
      return false;
    }
    if ($receiver === null) {
      return true;
    }
    $completed = $source->getEffectCompleted();
    if (!is_array($completed) || !isset($completed['universalToughScope'])) {
      return true;
    }
    $scope = $completed['universalToughScope'];
    if ($scope === 'expedition') {
      return in_array($receiver->getLocation(), STORMS);
    }
    if ($scope === 'expeditionAnchored') {
      return in_array($receiver->getLocation(), STORMS) && $receiver->hasToken(ANCHORED);
    }
    return true;
  }

  public function countUniversalToughAnchoredAsleep()
  {
    return count(
      $this->getPlayedCards()->filter(function ($card) {
        $dynamicTough = $card->getDynamicTough();
        if (!is_array($dynamicTough)) {
          return Utils::checkAttributeCondition('tough', $card->getDynamicTough(), $this, $card) == 'anchoredOrAsleep';
        } else {
          foreach ($dynamicTough as $singleTough) {
            if (Utils::checkAttributeCondition('tough', $singleTough, $this, $card) == 'anchoredOrAsleep') {
              return true;
            }
          }
          return false;
        }
      })
    );
  }

  /**
   * Highest universal Tough value from completed Feat landmarks (same key does not stack additively).
   */
  public function countUniversalLandmarksToughFromCompletedFeat()
  {
    $n = 0;
    foreach ($this->getLandmarks() as $card) {
      if (!$card->mayMergeCompletedFeatDynamicTough() || Meeples::countMeeples('card-' . $card->getId(), FEAT_COMPLETED) < 1) {
        continue;
      }
      $completed = $card->getEffectCompleted();
      if (empty($completed) || !is_array($completed)) {
        continue;
      }
      if (isset($completed['dynamicTough'])) {
        $dt = $completed['dynamicTough'];
        if (is_string($dt) && str_starts_with($dt, 'universalLandmarks')) {
          $suffix = substr($dt, strlen('universalLandmarks'));
          if ($suffix !== '') {
            $n = max($n, (int) $suffix);
          }
        }
      }
    }
    return $n;
  }

  public function countUniversalTokenGigantic()
  {
    return count(
      $this->getPlayedCards()->filter(function ($card) {
        $dynamicGigantic = $card->getDynamicGigantic();
        if (!is_array($dynamicGigantic) && $dynamicGigantic != '') {
          $dynamicGigantic = [$dynamicGigantic];
        } elseif ($dynamicGigantic == '') {
          $dynamicGigantic = [];
        }

        foreach ($dynamicGigantic as $singleGigantic) {
          $dynSplit = explode(':', $singleGigantic);
          if (count($dynSplit) > 1) {
            // we need to test if ok, add change dynamic tough to the value of 0
            if (!is_null(Utils::checkAttributeCondition('gigantic', $singleGigantic, $this, $card))) {
              return $dynSplit[0] == 'universalGiganticToken';
            }
          } else {
            return $singleGigantic == 'universalGiganticToken';
          }
        }
      })
    );
  }

  public function isInContact($location = null)
  {
    $opponent = Players::getNext($this);
    $opponentTokens = Meeples::getStormTokens($opponent->getId());

    if (is_null($location)) {
      $tokenF = ['getHeroToken', 'getCompanionToken'];
      if ($this->hasOverrideInContact(STORM_LEFT) || $this->hasOverrideInContact(STORM_RIGHT)) {
        return true;
      }
    } elseif ($location == STORM_LEFT) {
      $tokenF = ['getHeroToken'];
      if ($this->hasOverrideInContact(STORM_LEFT)) {
        return true;
      }
    } elseif ($location == STORM_RIGHT) {
      $tokenF = ['getCompanionToken'];
      if ($this->hasOverrideInContact(STORM_RIGHT)) {
        return true;
      }
    } else {
      return false;
    }

    foreach ($tokenF as $tok) {
      $token = $this->$tok()->getLocation();
      foreach ($opponentTokens as $mId => $meeple) {
        if ($token == $meeple->getLocation()) {
          return true;
        }
      }
    }
    return false;
  }

  public function hasOverrideInContact($location)
  {
    foreach ($this->getPlayedCards() as $cId => $card) {
      if (($card->getLocation() == $location || ($card->isGigantic() && in_array($card->getLocation(), STORMS))) && $card->isOverrideContact()) {
        return true;
      }
    }
    return false;
  }

  public function hasOverrideBehind($location)
  {
    foreach ($this->getPlayedCards() as $cId => $card) {
      if (($card->getLocation() == $location || ($card->isGigantic() && in_array($card->getLocation(), STORMS))) && $card->isOverrideBehind()) {
        return true;
      }
    }
    return false;
  }
}
