<?php

namespace ALT\Helpers;

use ALT\Core\Globals;
use ALT\Managers\Cards;
use ALT\Managers\Players;
use ALT\Managers\Meeples;
use ALT\Core\Notifications;

// Conditions
abstract class Conditions
{
  // Checking a condition/several conditions
  public static function check($power, $card, $event)
  {
    $conditions = [];
    if (isset($power['conditions']) && is_array($power['conditions'])) {
      $conditions = $power['conditions'];
    }

    if (isset($power['condition'])) {
      if (is_array($power['condition'])) {
        $conditions = array_merge($conditions, $power['condition']);
      } else {
        $conditions[] = $power['condition'];
      }
    }

    if (isset($power['cardId'])) {
      $event['cardId'] = $power['cardId'];
    }

    if (isset($power['wasGigantic'])) {
      $event['wasGigantic'] = $power['wasGigantic'];
    }

    foreach ($conditions as $cond) {
      $t = explode(':', $cond);
      $condFct = $t[0];
      $condArgs = array_slice($t, 1);

      if (self::$condFct($card, $event, ...$condArgs) === false) {
        // var_dump($card->getName(), $cond, $event);
        return false;
      }
    }
    return true;
  }

  public static function isFirstPassing($card, $event)
  {
    return Globals::getFirstPass() == $card->getPId();
  }


  ///////////////////////////////////////
  //    ____                           _ 
  //   / ___| ___ _ __   ___ _ __ __ _| |
  //  | |  _ / _ \ '_ \ / _ \ '__/ _` | |
  //  | |_| |  __/ | | |  __/ | | (_| | |
  //   \____|\___|_| |_|\___|_|  \__,_|_|
  ///////////////////////////////////////

  public static function isFromReserve($card, $event)
  {
    return ($event['from'] ?? null) == RESERVE;
  }

  public static function isFromHand($card, $event)
  {
    return ($event['from'] ?? null) == HAND;
  }
  
  public static function isToStorm($card, $event)
  {
    return in_array($event['to'] ?? null, STORMS);
  }

  public static function isToReserve($card, $event)
  {
    return ($event['to'] ?? null) == RESERVE;
  }

  public static function hasHeroSignatureToken($card, $event)
  {
    return !is_null($card->getPlayer()->getHeroSignatureTokenType());
  }

  public static function isAddedToMyExpedition($card, $event)
  {
    if (!isset($event['cardId'])) {
      return false;
    }
    $locationPId = $event['locationPId'] ?? -1;
    if ($card->getPId() != $locationPId) {
      return false;
    }
    return Cards::get($event['cardId'])->getPId() == $locationPId;
  }

  public static function isToDiscard($card, $event)
  {
    return ($event['to'] ?? null) == DISCARD_PILE;
  }

  public static function isStillSameLocation($card, $event)
  {
    if (!isset($event['cardId'])) {
      return false;
    }
    return Cards::get($event['cardId'])->getLocation() == ($event['to'] ?? '');
  }

  public static function isSourceSameLocation($card, $event)
  {
    return $card->getLocation() == ($event['sourceLocation'] ?? '');
  }

  public static function hasDeckCards($card, $event)
  {
    return $card->getPlayer()->getDeckCount() > 0;
  }

  public static function isInStorms($card, $event)
  {
    return in_array($card->getLocation(), STORMS);
  }

  public static function isSource($card, $event)
  {
    return $card->getId() == ($event['sourceId'] ?? -1);
  }

  public static function isSourceOrAugment($card, $event)
  {
    return $card->getId() == ($event['sourceId'] ?? -1) || (($event['augment'] ?? false) == true && ($event['cardId'] ?? -1) == $card->getId());
  }

  public static function isFacingSource($card, $event)
  {
    $targetCard = Cards::get($event['cardId']);
    return $card->getPId() != $targetCard->getPId() && ($card->getLocation() == $targetCard->getLocation() || $targetCard->isGigantic());
  }

  public static function hasSameOwner($card, $event)
  {
    $cardId = $event['cardId'] ??  null;
    if (is_null($cardId)) {
      return false;
    }

    if (is_null(Cards::getSingle($cardId, false))) {
      return ($event['pId'] ?? null) == $card->getPId();
    }

    // 186238 - Combo Treyst + brainwash
    if (($event['stealOwnership'] ?? false) && Cards::get($cardId)->getPId() != $card->getPId()) {
      return true;
    }

    // Controller is snapshotted at leave time (before borrow/steal ownership is restored in
    // discardTo). Needed for The Mess when a borrowed Character leaves your Expedition
    // (e.g. Romantic Encounter). Do not apply on sabotage — that would false-trigger
    // "your Reserve" effects like The Refinery when you sabotage an opponent's card.
    return Cards::get($cardId)->getPId() == $card->getPId()
      || (!($event['sabotage'] ?? false) && $card->getPId() == ($event['controller'] ?? -1));
  }

  /** True when the passive card's owner voluntarily discards one of their own cards. */
  public static function isDiscardByOwner($card, $event)
  {
    $cardId = $event['cardId'] ?? null;
    if (is_null($cardId)) {
      return false;
    }

    $discarded = Cards::getSingle($cardId, false);
    if (is_null($discarded) || $discarded->getPId() != $card->getPId()) {
      return false;
    }

    $controller = $event['controller'] ?? $event['pId'] ?? null;
    if ($controller != $card->getPId()) {
      return false;
    }

    if ($event['sabotage'] ?? false) {
      return ($event['from'] ?? null) == RESERVE;
    }

    return true;
  }

  public static function excludeSelf($card, $event)
  {
    return $card->getId() != ($event['cardId'] ?? ($event['sourceId'] ?? -1));
  }

  public static function isCardPlayedSameOwner($card, $event)
  {
    $cardId = $event['cardId'] ?? null;
    if (is_null($cardId)) {
      return false;
    }

    return $card->getPId() == Cards::get($cardId)->getPId();
  }

  ///////////////////////////////////////////////
  //  ____  _                         ___    _
  // |  _ \| | __ _ _   _  ___ _ __  |_ _|__| |
  // | |_) | |/ _` | | | |/ _ \ '__|  | |/ _` |
  // |  __/| | (_| | |_| |  __/ |     | | (_| |
  // |_|   |_|\__,_|\__, |\___|_|    |___\__,_|
  //                |___/
  ///////////////////////////////////////////////

  public static function isMe($card, $event)
  {
    return ($event['pId'] ?? null) == $card->getPId();
  }

  public static function isTemple($card, $event)
  {
    return $card->isPlayedAsTemple();
  }

  public static function isNotTemple($card, $event)
  {
    return !$card->isPlayedAsTemple();
  }
  
  /** True when this card is the one that entered play (playCard + cardId on the event). Used for InvokeToken with onPlay effects. */
  public static function isSelfPlayCardEvent($card, $event)
  {
    return ($event['playCard'] ?? false) && (int) ($event['cardId'] ?? -1) === (int) $card->getId();
  }

  public static function isNotMe($card, $event)
  {
    return ($event['pId'] ?? null) != $card->getPId();
  }

  public static function isFirstPlayer($card, $event)
  {
    return self::isMe($card, $event) && $card->getPId() == Globals::getFirstPlayer();
  }

  public static function isNotFirstPlayer($card, $event)
  {
    return self::isMe($card, $event) && $card->getPId() != Globals::getFirstPlayer();
  }

  ////////////////////////////////////////////////////////////
  //  ____                ____  _
  // |  _ \  __ _ _   _  |  _ \| |__   __ _ ___  ___  ___
  // | | | |/ _` | | | | | |_) | '_ \ / _` / __|/ _ \/ __|
  // | |_| | (_| | |_| | |  __/| | | | (_| \__ \  __/\__ \
  // |____/ \__,_|\__, | |_|   |_| |_|\__,_|___/\___||___/
  //              |___/
  ////////////////////////////////////////////////////////////

  public static function isMyTurn($card, $event)
  {
    return Globals::getActivePId() == $card->getPId();
  }

  public static function isAfternoon($card, $event)
  {
    return Globals::isDayPhase();
  }

  public static function isNoon($card, $event)
  {
    return Globals::getPhase() == 1;
  }

  public static function isNotFirstTurn($card, $event)
  {
    return Globals::getDay() != 1;
  }

  public static function isNight($card, $event)
  {
    return Globals::getPhase() == 4;
  }

  public static function isNotNight($card, $event)
  {
    return !self::isNight($card, $event);
  }

  public static function movesStormsWithForest($card, $event)
  {
    $stormMoves = Globals::getStormMoves();
    $storm = $event['expedition'] ?? $card->getLocation();
    if (
      !isset($stormMoves[$card->getPId()]) ||
      $card->getPId() != $event['pId'] ||
      !isset($stormMoves[$card->getPId()][$storm])
    ) {
      return false;
    }

    $move = $stormMoves[$card->getPId()][$storm];
    if (in_array(FOREST, $move['biomes']) && $move['moves'] >= 1) {
      return true;
    }

    return false;
  }

  public static function movesStormDueToAscension($card, $event)
  {
    $stormMoves = Globals::getStormMoves();
    $storm = $event['expedition'] ?? $card->getLocation();
    if (
      !isset($stormMoves[$card->getPId()]) ||
      $card->getPId() != $event['pId'] ||
      !isset($stormMoves[$card->getPId()][$storm])
    ) {
      return false;
    }

    $move = $stormMoves[$card->getPId()][$storm];
    if ($card->getPlayer()->isAscended($storm) && $move['moves'] >= 1 && $move['hasMovedFromAscension']) {
      return true;
    }

    return false;
  }

  public static function movesStormsDueToAscension($card, $event)
  {
    $stormMoves = Globals::getStormMoves();
    foreach (STORMS as $storm) {
        if (!isset($stormMoves[$card->getPId()]) || $card->getPId() != $event['pId'] ||
            !isset($stormMoves[$card->getPId()][$storm])) {
          return false;
        }
        $move = $stormMoves[$card->getPId()][$storm];
        if (!$card->getPlayer()->isAscended($storm) || !$move['hasMovedFromAscension']) {
          return false;
        }
    }
    return true;
  }

  public static function movesAnyExpeditionDueToAscension($card, $event)
  {
    $stormMoves = Globals::getStormMoves();
    foreach (STORMS as $storm) { 
      if (!isset($stormMoves[$card->getPId()]) || $card->getPId() != $event['pId'] || !isset($stormMoves[$card->getPId()][$storm]) ) { 
        continue;
      } 
      $move = $stormMoves[$card->getPId()][$storm]; 
      if ($card->getPlayer()->isAscended($storm) && $move['hasMovedFromAscension']) { 
        return true; 
      }  
    } 
    return false;
  }

  public static function movesAscendedAnyExpeditions($card, $event)
  {
    $stormMoves = Globals::getStormMoves();
    foreach (STORMS as $storm) {
      if (
        !isset($stormMoves[$card->getPId()]) ||
        $card->getPId() != $event['pId'] ||
        !isset($stormMoves[$card->getPId()][$storm])
      ) {
        return false;
      }

      // $side = $storm == STORM_LEFT ? HERO : COMPANION;
      $move = $stormMoves[$card->getPId()][$storm];
      if ($card->getPlayer()->isAscended($storm) && $move['moves'] >= 1) {
        return true;
      }
    }
    return false;
  }

  public static function zhenZephyr($card, $event)
  {
    $stormMoves = Globals::getStormMoves();
    foreach (STORMS as $storm) {
      // $side = $storm == STORM_LEFT ? HERO : COMPANION;
      // are there characters facing it?
      // if (Players::getNext($card->getPlayer())->countCardsInLocation($storm, [CHARACTER]) > 0) {
      //   continue;
      // }

      $found = false;
      foreach (Players::getNext($card->getPlayer())->getPlayedCards() as $cId => $pCard) {
        if ($pCard->getType() != CHARACTER) {
          continue;
        }

        if ($pCard->getLocation() == $storm || (in_array($pCard->getLocation(), STORMS) && $pCard->isGigantic())) {
          $found = true;
          break;
        }
      }
      if ($found) {
        continue;
      }

      // is it ascended?
      if (($stormMoves[$card->getPId()][$storm]['ascended'] ?? false) === false) {
        continue;
      }
      if (($stormMoves[$card->getPId()][$storm]['moves'] ?? 0) >= 1) {
        return true;
      }
    }
    return false;
  }

  public static function zhenZephyrMoveExpedition($card, $event)
  {
    $storm = $event['expedition'];
    if ($event['moveExpedition'] < 1 || $event['ascended'] == false) {
      return false;
    }

    $side = $storm == STORM_LEFT ? HERO : COMPANION;
    // are there characters facing it?

    $found = false;
    foreach (Players::getNext($card->getPlayer())->getPlayedCards() as $cId => $pCard) {
      if ($pCard->getType() != CHARACTER) {
        continue;
      }
      if ($pCard->getLocation() == $storm || (in_array($pCard->getLocation(), STORMS) && $pCard->isGigantic())) {
        return false;
      }
    }

    return true;
  }

  public static function hasNotMoved($card, $event)
  {
    return $event['pId'] == $card->getPId() &&
      (!isset(Globals::getStormMoves()[$card->getPId()]) ||
        (abs(Globals::getStormMoves()[$card->getPId()][STORM_LEFT]['moves'] ?? 0)) +
        (abs(Globals::getStormMoves()[$card->getPId()][STORM_RIGHT]['moves'] ?? 0)) ==
        0) && !Globals::isTieBreakerMode();
  }

  public static function myExpeditionHasNotMoved($card, $event)
  {
    $stormMoves = Globals::getStormMoves()[$card->getPId()] ?? null;
    $stormMoves = $stormMoves[$card->getLocation()] ?? null;
    return $event['pId'] == $card->getPId() && (is_null($stormMoves) || ($stormMoves['moves'] ?? 0) == 0) && !Globals::isTieBreakerMode() && (!isset($event['expedition']) || $event['expedition'] == $card->getLocation());;
  }

  public static function myOtherExpeditionHasMoved($card, $event)
  {
    $stormMoves = Globals::getStormMoves()[$card->getPId()] ?? null;
    $otherExp = $card->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
    $stormMoves = $stormMoves[$otherExp] ?? null;
    return $event['pId'] == $card->getPId() && (!is_null($stormMoves) && ($stormMoves['moves'] ?? 0) >= 0) && !Globals::isTieBreakerMode() && (!isset($event['expedition']) || $event['expedition'] == $otherExp);;
  }

  public static function myExpeditionHasMoved($card, $event)
  {
    $stormMoves = Globals::getStormMoves()[$card->getPId()] ?? null;
    $stormMoves = $stormMoves[$card->getLocation()] ?? null;
    return $event['pId'] == $card->getPId() && !is_null($stormMoves) && ($stormMoves['moves'] ?? 0) > 0 && !Globals::isTieBreakerMode() && (!isset($event['expedition']) || $event['expedition'] == $card->getLocation());
  }
  
  public static function anyOfMyExpeditionsHasMoved($card, $event)
  {
    if (($event['pId'] ?? null) != $card->getPId() || Globals::isTieBreakerMode()) {
      return false;
    }

    $stormMoves = Globals::getStormMoves()[$card->getPId()] ?? [];
    foreach (STORMS as $storm) {
      if (($stormMoves[$storm]['moves'] ?? 0) > 0) {
        return true;
      }
    }

    return false;
  }

  public static function myExpeditionIsBehind($card, $event)
  {
    return self::isMyExpeditionAheadOrBehind($card, $event, false);
  }

  public static function myExpeditionIsAhead($card, $event)
  {
    return self::isMyExpeditionAheadOrBehind($card, $event, true);
  }

  private static function isMyExpeditionAheadOrBehind($card, $event, bool $ahead)
  {
    if ($card->getPlayer()->hasOverrideBehind($card->getLocation())) {
      return !$ahead;
    }

    if ($card->getId() != ($event['cardId'] ?? -1)) {
      $location = $card->getLocation();
    } else {
      $location = in_array(($event['to'] ?? $card->getLocation()), ['limbo', LANDMARK]) ? $card->getLocation() : ($event['to'] ?? $card->getLocation());
    }
    $win = Players::getWinningPlayerByStorms()[$location] ?? null;
    if (Globals::isTieBreakerMode() || is_null($win) || $win == -1) {
      return false;
    }

    $isAhead = $win == $card->getPId();
    return $ahead ? $isAhead : !$isAhead;
  }

  public static function companionExpeditionIsBehind($card, $event)
  {
    $winners = Players::getWinningPlayerByStorms();
    if ($card->getPlayer()->hasOverrideBehind(STORM_RIGHT)) {
      return true;
    }
    $win = $winners[STORM_RIGHT];
    return !is_null($win) && $win != -1 && $win != $card->getPId();
  }

  public static function heroExpeditionIsBehind($card, $event)
  {
    $winners = Players::getWinningPlayerByStorms();
    if ($card->getPlayer()->hasOverrideBehind(STORM_LEFT)) {
      return true;
    }
    $win = $winners[STORM_LEFT];
    return !is_null($win) && $win != -1 && $win != $card->getPId();
  }

  public static function myExpeditionIsNotBehind($card, $event)
  {
    return !self::myExpeditionIsBehind($card, $event);
  }

  public static function cardPlayedExpeditionIsBehind($card, $event)
  {
    $pCard = Cards::get($event['cardId']);
    return self::myExpeditionIsBehind($pCard, $event);
  }

  public static function allExpeditionsAreBehindOrTied($card, $event)
  {
    $winners = Players::getWinningPlayerByStorms();
    $player = $card->getPlayer();
    if ($card->getPlayer()->hasOverrideBehind(STORM_LEFT)) {
      return true;
    }
    $left = $player->hasOverrideBehind(STORM_LEFT) ? -1 : $winners[STORM_LEFT];
    $right = $player->hasOverrideBehind(STORM_RIGHT) ? -1 : $winners[STORM_RIGHT];
    return !is_null($left) && !is_null($right) && $left != $card->getPId() && $right != $card->getPId();
  }

  public static function allExpeditionsAreNotBehindOrTied($card, $event)
  {
    return !self::allExpeditionsAreBehindOrTied($card, $event);
  }

  public static function cardPlayedAscended($card, $event)
  {
    $pCard = Cards::get($event['cardId']);
    return self::isCardExpeditionAscended($pCard, $event);
  }

  /////////////////////////////////////////
  //   ____            _             _
  //  / ___|___  _ __ | |_ _ __ ___ | |
  // | |   / _ \| '_ \| __| '__/ _ \| |
  // | |__| (_) | | | | |_| | | (_) | |
  //  \____\___/|_| |_|\__|_|  \___/|_|
  /////////////////////////////////////////

  public static function canPay($card, $event, $n)
  {
    return $card->getPlayer()->getMana() >= $n;
  }

  public static function hasCardsInHand($card, $event)
  {
    return $card
      ->getPlayer()
      ->getHand()
      ->count() > 0;
  }

  public static function hasXCardsInHand($card, $event, $n, $op = 'GTE')
  {
    $count = $card
      ->getPlayer()
      ->getHand()
      ->count();

    if ($op == 'GTE') {
      return $count >= $n;
    }
    if ($op == 'LTE') {
      return $count <= $n;
    }
    if ($op == 'EQ') {
      return $count == $n;
    }
    die('Unknown op for hasXCardsInHand');
  }

  public static function hasXCardsInHandExceptCurrentCard($card, $event, $n, $op = 'GTE')
  {
    $count = $card->getLocation() == RESERVE ? $card
      ->getPlayer()
      ->getHand()
      ->count() : $card
      ->getPlayer()
      ->getHand()
      ->count() - 1;

    if ($op == 'GTE') {
      return $count >= $n;
    }
    if ($op == 'LTE') {
      return $count <= $n;
    }
    if ($op == 'EQ') {
      return $count == $n;
    }
    die('Unknown op for hasXCardsInHandExceptCurrentCard');
  }

  public static function hasNoTokensInLandmarks($card, $event)
  {
    $cards = $card->getPlayer()->getPlayedCards()->filter(function ($c) {
      return $c->getLocation() == LANDMARK && $c->isToken();
    });
    return $cards->count() == 0;
  }

  public static function has6HandCostLandmarks($card, $n)
  {
    $cards = $card->getPlayer()->getLandmarks();
    $total = 0;
    foreach ($cards as $c) {
      $total += $c->getCostHand();
    }
    return $total >= 6;
  }  

  public static function hasDiscardPileCards($card, $event, $n, $op = 'GTE')
  {
    // Use the cards manager directly to avoid relying on a specific player model accessor
    // during reaction pre-checks, where the card context can be partially hydrated.
    $count = Cards::getFiltered($card->getPId(), DISCARD_PILE)->count();
    if ($op == 'GTE') {
      return $count >= $n;
    }
    if ($op == 'LTE') {
      return $count <= $n;
    }
    if ($op == 'EQ') {
      return $count == $n;
    }
    die('Unknown op for hasDiscardPileCards');
  }

   public static function hasEverReachXDiscardedCards($card, $event, $n, $op = 'GTE')
  {
    $pId = $card->getPId();
    $tracker = Globals::getSamSpookEverDiscarded();

    if (isset($tracker[$pId]) && $tracker[$pId]) {
      return true;
    }

    $count = Cards::getFiltered($pId, DISCARD_PILE)->count();
    $met = ($op == 'GTE' && $count >= $n) ||
           ($op == 'LTE' && $count <= $n) ||
           ($op == 'EQ' && $count == $n);

    if ($met) {
      $tracker[$pId] = true;
      Globals::setSamSpookEverDiscarded($tracker);
    }

    return $met;
  }
  
  public static function hasCardInDiscardPile($card, $event, $name)
  {
    if (is_null($name) || $name === '') {
      return false;
    }

    $discardedCards = Cards::getFiltered($card->getPId(), DISCARD_PILE);
    foreach ($discardedCards as $discardedCard) {
      if (self::isCardMatchingSearch($discardedCard, $name)) {
        return true;
      }
    }

    return false;
  }

  public static function hasCardInDiscardPileSourceName($card, $event)
  {
    return self::countCardsInDiscardPileSourceName($card, $event) > 0;
  }

  public static function countCardsInDiscardPileSourceName($card, $event)
  {
    $name = self::getCardSearchName($card);
    if ($name === '') {
      return 0;
    }

    $count = 0;
    $discardedCards = Cards::getFiltered($card->getPId(), DISCARD_PILE);
    foreach ($discardedCards as $discardedCard) {
      if (self::isCardMatchingSearch($discardedCard, $name)) {
        $count++;
      }
    }

    return $count;
  }

  /**
   * Card identity segment used to match same-name printings (e.g. YZ_115 from ALT_EOLE_B_YZ_115_C).
   */
  public static function getCardSearchName($card)
  {
    $uid = (string) $card->getUid();
    if ($uid === '') {
      return '';
    }

    if (preg_match('/^ALT_[^_]+_[^_]+_([A-Z]{2}_\d+)/', $uid, $matches) === 1) {
      return $matches[1];
    }

    return '';
  }

  /**
   * True when the card UID contains $name as a set/faction segment (e.g. YZ_115 matches ALT_EOLE_B_YZ_115_C).
   * Uses /_<name>(_|$)/ so partial numbers like YZ_1150 do not match.
   */
  public static function isCardMatchingSearch($card, $name)
  {
    if ($name === null || $name === '') {
      return false;
    }

    $uid = (string) $card->getUid();
    if ($uid === '') {
      return false;
    }

    $pattern = '/_' . preg_quote((string) $name, '/') . '(_|$)/';
    return preg_match($pattern, $uid) === 1;
  }
  
  /** True if this player has n completed feat. */
  public static function hasCompletedFeat($card, $event, $n = 1, $op = 'GTE')
  {
    return self::hasControlFeatWithMaxBaseCost($card, $event, $n, false, 99, 'completed', $op);
  }

  /** True if this card has no feat-completed meeple (COMPLETE_FEAT passive not yet applied). */
  public static function isThisFeatIncomplete($card, $event)
  {
    return Meeples::countMeeples('card-' . $card->getId(), FEAT_COMPLETED) < 1;
  }

  /** True if this card has a feat-completed meeple. */
  public static function isThisFeatCompleted($card, $event)
  {
    return Meeples::countMeeples('card-' . $card->getId(), FEAT_COMPLETED) >= 1;
  }

  /**
   * True if the card indicated by ctx cardId has a completed-Feat meeple.
   * Must run before sacrificing that card to discard — discardTo() removes meeples including FEAT_COMPLETED.
   */
  public static function isTargetFeatCompleted($card, $event)
  {
    $cardId = $event['cardId'] ?? null;
    if ($cardId === null) {
      return false;
    }
    $c = Cards::get($cardId);
    return Meeples::countMeeples('card-' . $c->getId(), FEAT_COMPLETED) >= 1;
  }
  
  public static function hasControlCharacterWithMinBaseCost($card, $event, $minBaseCost, $n = 1, $op = 'GTE')
  {
    $minBaseCost = (int) $minBaseCost;
    $n = (int) $n;
    $cards = $card->getPlayer()->getPlayedCards()->filter(function ($c) use ($minBaseCost) {
      if ($c->getType() != CHARACTER && !in_array(CHARACTER, $c->getAdditionalType())) {
        return false;
      }
      $baseCost = $c->hasToken(FLEETING) ? $c->getCostReserve() : $c->getCostHand();
      return $baseCost >= $minBaseCost;
    });
    $m = $cards->count();
    if ($op == 'GTE') {
      return $m >= $n;
    }
    if ($op == 'LTE') {
      return $m <= $n;
    }
    if ($op == 'EQ') {
      return $m == $n;
    }
    throw new \BgaVisibleSystemException('Unknown op for hasControlCharacterWithMinBaseCost: ' . $op);
  }

  /**
   * Counts Feat permanents you control in play (same zones / filters as hasControl for subtype feat).
   *
   * Condition string segments (after the name), same order as hasControl minus type and opponent:
   *   hasControlFeat
   *   hasControlFeat:{n}
   *   hasControlFeat:{n}:{excludeMyself true|false}
   *   hasControlFeat:{n}:{excludeMyself}:{state all|completed|notcompleted|exhausted}
   *   hasControlFeat:{n}:{excludeMyself}:{state}:{op GTE|LTE|EQ|LT|GT}
   */
  public static function hasControlFeat($card, $event, $n = 1, $excludeMyself = 'false', $state = 'all', $op = 'GTE')
  {
    return self::hasControl($card, $event, FEAT, (int) $n, $excludeMyself, $state, $op, false);

  }
  
  /**
   * Feat permanents in play whose min(Hand, Reserve cost) is at most maxBaseCost; same optional segments as hasControlFeat after maxBaseCost.
   */
  public static function hasControlFeatWithMaxBaseCost(
    $card,
    $event,
    $n = 1,
    $excludeMyself = 'false',
    $maxBaseCost = 99,
    $state = 'all',
    $op = 'GTE'
  ) {
    $player = $card->getPlayer();
    $types = [CHARACTER, TOKEN, PERMANENT, SPELL];
    $cards = $player->getPlayedCards()->filter(function ($c) use ($types) {
      return in_array($c->getType(), $types) || count(array_intersect($types, $c->getAdditionalType())) > 0;
    });
    $cards = $cards->filter(fn($c) => in_array(FEAT, $c->getSubtypes()));
    $maxBaseCost = (int) $maxBaseCost;
    $cards = $cards->filter(function ($c) use ($maxBaseCost) {
      return min($c->getCostHand(), $c->getCostReserve()) <= $maxBaseCost;
    });
    if ($excludeMyself === 'true') {
      $cards = $cards->filter(fn($c) => $c->getId() != $card->getId());
    }
    if ($state != 'all') {
      if ($state == 'exhausted') {
        $cards = $cards->filter(fn($c) => $c->isTapped());
      }
      elseif ($state == 'completed') {
        $cards = $cards->filter(fn($c) => Meeples::countMeeples('card-' . $c->getId(), FEAT_COMPLETED) > 0);
      }
      elseif ($state == 'notcompleted') {
        $cards = $cards->filter(fn($c) => Meeples::countMeeples('card-' . $c->getId(), FEAT_COMPLETED) == 0);
      }
    }
    $m = $cards->count();
    $n = (int) $n;
    if ($op == 'GTE') {
      return $m >= $n;
    }
    if ($op == 'LTE') {
      return $m <= $n;
    }
    if ($op == 'EQ') {
      return $m == $n;
    }
    if ($op == 'LT') {
      return $m < $n;
    }
    if ($op == 'GT') {
      return $m > $n;
    }
    throw new \BgaVisibleSystemException('Unknown op for hasControlFeatWithMaxBaseCost: ' . $op);
  }

  public static function hasBiggerHand($card, $event)
  {
    return $card
      ->getPlayer()
      ->getHand()
      ->count() > Players::getNext($card->getPlayer())->getHand()->count();
  }

  public static function hasSmallerHand($card, $event)
  {
    return $card
      ->getPlayer()
      ->getHand()
      ->count() < Players::getNext($card->getPlayer())->getHand()->count();
  }

  public static function canSabotage($card, $event)
  {
    return Cards::getInLocation(RESERVE)->count() > 0;
  }

  public static function hasReserve($card, $event, $type = null, $costHand = null, $costReserve = null,  $op = 'GTE', $state = 'all', $n = 0)
  {
    $cards = $card
      ->getPlayer()
      ->getReserveCards();

    if (!is_null($type) && $type != '' && in_array($type, TYPES)) {
      $cards = $cards->filter(function ($c) use ($type) {
        return $c->getType() == $type || in_array($type, $c->getAdditionalType());
      });
    }

    if (!is_null($type) && $type != '' && in_array($type, SUBTYPES)) {
      $cards = $cards->filter(function ($c) use ($type) {
        return in_array($type, $c->getSubtypes());
      });
    }

    if (!is_null($costHand) && $costHand != '') {
      $cards = $cards->filter(function ($c) use ($costHand, $op) {
        if ($op == 'GTE') {
          return $c->getCostHand() >= $costHand;
        } elseif ($op == 'LTE') {
          return $c->getCostHand() <= $costHand;
        }
      });
    }

    if (!is_null($costReserve) && $costReserve != '') {
      $cards = $cards->filter(function ($c) use ($costReserve, $op) {
        if ($op == 'GTE') {
          return $c->getCostReserve() >= $costReserve;
        } elseif ($op == 'LTE') {
          return $c->getCostReserve() <= $costReserve;
        }
      });
    }

    if ($state != 'all') {
      if ($state == 'boosted') {
        $cards = $cards->filter(fn($c) => $c->hasToken(BOOST));
      } elseif ($state == 'fleeting') {
        $cards = $cards->filter(fn($c) => $c->hasToken(FLEETING));
      } elseif ($state == 'exhausted') {
        $cards = $cards->filter(fn($c) => $c->isTapped());
      }
    }
    return $cards->count() > $n;
  }
  
  public static function hasOtherSupportCardInReserveOrExpeditions($card, $event)
  {
    $player = $card->getPlayer();
    $otherCards = $player->getReserveCards()
      ->merge($player->getPlayedCards())
      ->filter(function ($c) use ($card) {
        if ($c->getId() == $card->getId()) {
          return false;
        }
        if ($c->getLocation() == LANDMARK || $c->getType() == HERO) {
          return false;
        }
        return !empty($c->getEffectSupport());
      });

    return !$otherCards->empty();
  }

  public static function hasOtherAnimalInReserveOrExpeditions($card, $event)
  {
    $player = $card->getPlayer();
    $otherCards = $player->getReserveCards()
      ->merge($player->getPlayedCards())
      ->filter(function ($c) use ($card) {
        if ($c->getId() == $card->getId()) {
          return false;
        }
        if ($c->getLocation() == LANDMARK || $c->getType() == HERO) {
          return false;
        }
        return in_array(ANIMAL, $c->getSubtypes());
      });

    return !$otherCards->empty();
  }

  public static function hasCompanionsInExpeditions($card, $event)
  {
    $pId = (int) $card->getPId();
    $inExpeditions = Cards::getPlayedCards($pId, [CHARACTER, TOKEN])->filter(function ($c) use ($pId) {
      if ((int) $c->getPId() !== $pId) {
        return false;
      }
      return $c->isGigantic() || in_array($c->getLocation(), STORMS, true);
    });

    foreach ($inExpeditions as $c) {
      if (in_array(COMPANION, $c->getSubtypes(), true)) {
        return true;
      }
    }

    return false;
  }

  public static function checkReserveCards($card, $event, $n, $op = 'GTE')
  {
    $count = $card
      ->getPlayer()
      ->getReserveCards()
      ->count();
    if ($op == 'GTE') {
      return $count >= $n;
    }
    if ($op == 'LTE') {
      return $count <= $n;
    }
  }
  
  public static function checkAbilityActivatedThisTurn($card, $event, $type = 'any')
  {
    $abilities = Globals::getAbilityActivatedThisTurn();
    $playerAbilities = $abilities[$card->getPId()] ?? [];
    if ($type == 'any') {
      return !empty($playerAbilities);
    }
    return !empty($playerAbilities[$type]);
  }
  
  public static function checkAbilityActivatedThisTurnTypeCount($card, $event, $type, $n = 1, $op = 'GTE')
  {
    $abilities = Globals::getAbilityActivatedThisTurnTypeCount();
    $count = ($abilities[$card->getPId()] ?? [])[$type] ?? 0;
    if ($op == 'GTE') {
      return $count >= $n;
    } elseif ($op == 'LTE') {
      return $count <= $n;
    } elseif ($op == 'LT') {
      return $count < $n;
    } elseif ($op == 'GT') {
      return $count > $n;
    }
    return false;
  }

  public static function hasLessReserveCards($card, $event)
  {
    $count = $card
      ->getPlayer()
      ->getReserveCards()
      ->count();
    $opponent = Players::getNext($card->getPlayer())->getReserveCards()
      ->count();
    return $count < $opponent;
  }

  public static function hasLess8Mana($card, $event)
  {
    return $card->getPlayer()->getTotalMana() < 8;
  }

  public static function hasXMana($card, $event, $n, $op = 'GTE')
  {
    $mana = $card->getPlayer()->getTotalMana();
    if ($op == 'GTE') {
      return $mana >= $n;
    } elseif ($op == 'LTE') {
      return $mana <= $n;
    } elseif ($op == 'LT') {
      return $mana < $n;
    } elseif ($op == 'GT') {
      return $mana > $n;
    }
  }

  public static function ownerOneReserve($card, $event)
  {
    $player = Cards::get($event['cardId'])->getPlayer();
    return $player->getReserveCards()->count() == 1;
  }

  public static function hasControl($card, $event, $type, $n, $excludeMyself = 'false', $state = 'all', $op = 'GTE', $opponent = false)
  {
    $types = [CHARACTER, TOKEN];
    $subTypes = null;
    if ($type == TOKEN) {
      $types = [CHARACTER, PERMANENT];
    }
    if ($type == PERMANENT) {
      $types = [PERMANENT];
    }
    if (in_array($type, SUBTYPES)) {
      $types = [CHARACTER, TOKEN, PERMANENT, SPELL];
    }
    if (strpos($type, '|') !== false) {
      $maybeSubtypes = explode('|', $type);
      $allSubtypes = count(array_intersect($maybeSubtypes, SUBTYPES)) == count($maybeSubtypes);
      if ($allSubtypes) {
        $subTypes = $maybeSubtypes;
        $types = [CHARACTER, TOKEN, PERMANENT, SPELL];
      }
    }
    

    if ($opponent) {
      $player = Players::getNext($card->getPlayer());
    } else {
      $player = $card->getPlayer();
    }
    $cards = $player->getPlayedCards()->filter(function ($c) use ($types) {
      return in_array($c->getType(), $types) || count(array_intersect($types, $c->getAdditionalType())) > 0;
    });

    if ($type == TOKEN) {
      $cards = $cards->filter(fn($c) => $c->isToken());
    }

    if (in_array($type, SUBTYPES)) {
      $cards = $cards->filter(fn($c) => in_array($type, $c->getSubtypes()));
    }
    if (!is_null($subTypes)) {
      $cards = $cards->filter(fn($c) => count(array_intersect($subTypes, $c->getSubtypes())) > 0);
    }

    if ($excludeMyself === 'true') {
      $cards = $cards->filter(fn($c) => $c->getId() != $card->getId());
    }

    if ($state != 'all') {
      if ($state == 'boosted') {
        $cards = $cards->filter(fn($c) => $c->hasToken(BOOST));
      } elseif ($state == 'fleeting') {
        $cards = $cards->filter(fn($c) => $c->hasToken(FLEETING));
      } elseif ($state == 'exhausted') {
        $cards = $cards->filter(fn($c) => $c->isTapped());
      }
    }
    $m = $cards->count();
    if ($op == 'GTE') {
      return $m >= $n;
    }
    if ($op == 'LTE') {
      return $m <= $n;
    }
  }
  
  public static function hasOpponentControl($card, $event, $type, $n, $excludeMyself = 'false', $state = 'all', $op = 'GTE')
  {
    return self::hasControl($card, $event, $type, $n, $excludeMyself, $state, $op, true);
  }

  public static function hasControlInReserveOrExpeditions($card, $event, $type, $n, $excludeMyself = 'false', $state = 'all', $op = 'GTE')
  {
    $types = [CHARACTER, TOKEN];
    $subTypes = null;
    if ($type == TOKEN) {
      $types = [CHARACTER, PERMANENT];
    }
    if ($type == PERMANENT) {
      $types = [PERMANENT];
    }
    if (in_array($type, SUBTYPES)) {
      $types = [CHARACTER, TOKEN, PERMANENT, SPELL];
    }
    if (strpos($type, '|') !== false) {
      $maybeSubtypes = explode('|', $type);
      $allSubtypes = count(array_intersect($maybeSubtypes, SUBTYPES)) == count($maybeSubtypes);
      if ($allSubtypes) {
        $subTypes = $maybeSubtypes;
        $types = [CHARACTER, TOKEN, PERMANENT, SPELL];
      }
    }

    $player = $card->getPlayer();
    $cards = $player->getReserveCards()
      ->merge($player->getPlayedCards())
      ->filter(function ($c) use ($types) {
        return in_array($c->getType(), $types) || count(array_intersect($types, $c->getAdditionalType())) > 0;
      });

    if ($type == TOKEN) {
      $cards = $cards->filter(fn($c) => $c->isToken());
    }

    if (in_array($type, SUBTYPES)) {
      $cards = $cards->filter(fn($c) => in_array($type, $c->getSubtypes()));
    }
    if (!is_null($subTypes)) {
      $cards = $cards->filter(fn($c) => count(array_intersect($subTypes, $c->getSubtypes())) > 0);
    }

    if ($excludeMyself === 'true') {
      $cards = $cards->filter(fn($c) => $c->getId() != $card->getId());
    }

    if ($state != 'all') {
      if ($state == 'boosted') {
        $cards = $cards->filter(fn($c) => $c->hasToken(BOOST));
      } elseif ($state == 'fleeting') {
        $cards = $cards->filter(fn($c) => $c->hasToken(FLEETING));
      } elseif ($state == 'exhausted') {
        $cards = $cards->filter(fn($c) => $c->isTapped());
      }
    }
    $m = $cards->count();
    if ($op == 'GTE') {
      return $m >= $n;
    }
    if ($op == 'LTE') {
      return $m <= $n;
    }
  }

  public static function hasControlExhaustedPermanentOrReserve($card, $event)
  {
    return self::hasControl($card, $event, PERMANENT, 1, false, 'exhausted') || self::hasReserve($card, $event, null, null, null, 'GTE', 'exhausted');
  }

  public static function has4PermanentsOrReserveExhausted($card, $event)
  {
    $cards = $card->getPlayer()->getPlayedCards()->filter(function ($c) {
      return (in_array($c->getType(), [PERMANENT]) || count(array_intersect([PERMANENT], $c->getAdditionalType())) > 0) && $c->isTapped();
    });

    $cards = $cards->merge($card->getPlayer()->getReserveCards()->filter(function ($c) {
      return $c->isTapped();
    }));
    return $cards->count() >= 4;
  }

  // Flawed prototype
  public static function noRobotnoPermanent($card, $event)
  {
    $cards = $card->getPlayer()->getPlayedCards();

    $cards = $cards->filter(function ($c) use ($card) {
      if ($c->getId() == $card->getId()) {
        return false;
      }
      if ($c->getType() == PERMANENT || in_array(ROBOT, $c->getSubtypes()) || in_array(PERMANENT, $c->getAdditionalType())) {
        return true;
      }
      return false;
    });
    return $cards->count() == 0;
  }

  public static function noPlantnoPermanent($card, $event)
  {
    $cards = $card->getPlayer()->getPlayedCards();

    $cards = $cards->filter(function ($c) use ($card) {
      if ($c->getId() == $card->getId()) {
        return false;
      }
      if ($c->getType() == PERMANENT || in_array(PLANT, $c->getSubtypes()) || in_array(PERMANENT, $c->getAdditionalType())) {
        return true;
      }
      return false;
    });
    return $cards->count() == 0;
  }

  public static function costCheck($card, $event, $cost, $op = 'GTE')
  {
    $discardedCard = Cards::get($event['cardId']);
    $costHand = $discardedCard->getCostHand();

    if ($op == 'GTE') {
      return $cost <= $costHand;
    }
    if ($op == 'LTE') {
      return $cost >= $costHand;
    }
  }

  public static function has3WithZeroStat($card, $event)
  {
    $playedCards = $card->getPlayer()->getPlayedCards();
    $hasZero = 0;
    foreach ($playedCards as $cId => $playedCard) {
      if (!in_array($playedCard->getType(), [TOKEN, CHARACTER])) {
        continue;
      }

      foreach ($playedCard->getBiomes() as $biome => $value) {
        if ($value == 0) {
          $hasZero++;
        }
      }
    }

    return $card->getPId() == ($event['pId'] ?? $card->getPId()) && $hasZero >= 3;
  }

  public static function hasXWithZeroStat($card, $event, $location = 'all', $n = 1, $op = 'GTE')
  {
    $playedCards = $card->getPlayer()->getPlayedCards();
    if ($location == 'all') {
      $playedCards = $playedCards->merge($card->getPlayer()->getReserveCards());
    }
    $hasZero = 0;
    foreach ($playedCards as $cId => $playedCard) {
      if (!in_array($playedCard->getType(), [TOKEN, CHARACTER])) {
        continue;
      }

      foreach ($playedCard->getBiomes() as $biome => $value) {
        if ($value == 0) {
          $hasZero++;
        }
      }
    }

    if ($hasZero >= $n && $op == 'GTE') {
      return true;
    } elseif ($hasZero <= $n && $op == 'LTE') {
      return true;
    }

    return false;
  }

  public static function canSacrifice($card, $event)
  {
    return self::hasControl($card, $event, CHARACTER, 1);
  }

  public static function hasControlFleetingAnchoredAsleep($card, $event)
  {
    $asleep = [];
    $anchored = [];
    $fleeting = [];
    foreach ($card->getPlayer()->getPlayedCards([TOKEN, CHARACTER]) as $cId => $c) {
      if ($c->hasToken(ASLEEP)) {
        $asleep[] = $cId;
      }
      if ($c->hasToken(ANCHORED)) {
        $anchored[] = $cId;
      }
      if ($c->hasToken(FLEETING)) {
        $fleeting[] = $cId;
      }
    }

    $combination = Utils::cartesian([$asleep, $anchored, $fleeting]);
    Utils::filter($combination, function ($comb) {
      return count(array_unique($comb)) == 3;
    });

    return count($combination) >= 1;
  }

  public static function isInContact($card, $event)
  {
    if (!in_array($card->getLocation(), STORMS)) {
      return false;
    }

    // Reka headset
    if ($card->getPlayer()->hasOverrideInContact($card->getLocation())) {
      return true;
    }

    $opponent = Players::getNext($card->getPlayer());
    $opponentTokens = Meeples::getStormTokens($opponent->getId());
    $tokenF = [$card->getLocation() == STORM_LEFT ? 'getHeroToken' : 'getCompanionToken'];
    if ($card->isGigantic()) {
      $tokenF = ['getHeroToken', 'getCompanionToken'];
    }

    foreach ($tokenF as $tok) {
      $token = $card->getPlayer()->$tok()->getLocation();
      foreach ($opponentTokens as $mId => $meeple) {
        if ($token == $meeple->getLocation()) {
          return true;
        }
      }
    }
    return false;
  }

  public static function allInContact($card, $event)
  {
    return $card->getPlayer()->isInContact(STORM_LEFT) && $card->getPlayer()->isInContact(STORM_RIGHT);
  }

  public static function isNotInContact($card, $event)
  {
    return !self::isInContact($card, $event);
  }

  public static function hasOneContact($card, $event)
  {
    return $card->getPlayer()->isInContact(STORM_LEFT) || $card->getPlayer()->isInContact(STORM_RIGHT);
  }

  ///////////////////////////////////////////////////////////////////////////////
  //   ____              _   ____                            _   _
  //  / ___|__ _ _ __ __| | |  _ \ _ __ ___  _ __   ___ _ __| |_(_) ___  ___
  // | |   / _` | '__/ _` | | |_) | '__/ _ \| '_ \ / _ \ '__| __| |/ _ \/ __|
  // | |__| (_| | | | (_| | |  __/| | | (_) | |_) |  __/ |  | |_| |  __/\__ \
  //  \____\__,_|_|  \__,_| |_|   |_|  \___/| .__/ \___|_|   \__|_|\___||___/
  //                                        |_|
  ///////////////////////////////////////////////////////////////////////////////

  public static function notFleeting($card, $event)
  {
    return !$card->hasToken(FLEETING) && !($event['fleeting'] ?? false);
  }

  public static function isAsleep($card, $event)
  {
    return $card->hasToken(ASLEEP);
  }

  public static function isAnchored($card, $event)
  {
    return $card->hasToken(ANCHORED);
  }

  public static function isNotAnchored($card, $event)
  {
    return !self::isAnchored($card, $event);
  }

  public static function hasFleeting($card, $event)
  {
    return $card->hasToken(FLEETING) || ($event['fleeting'] ?? false);
  }

  public static function leaveAndFleeting($card, $event)
  {
    return ($event['fleeting'] ?? false);
  }

  public static function notTapped($card, $event)
  {
    return !$card->isTapped();
  }

  public static function notUsed($card, $event)
  {
    return ($card->getExtraDatas()['userPower'] ?? false) == false;
  }
  
  public static function isUsed($card, $event)
  {
    return ($card->getExtraDatas()['userPower'] ?? false) == true;
  }
  
  public static function getSmokeThemOutCharges($pId, $cardId = null)
  {
    $val = Globals::getSmokeThemOutArmed()[$pId] ?? 0;
    if (is_int($val) || (is_string($val) && ctype_digit($val))) {
      return (int) $val;
    }
    if ($val) {
      // Legacy saves stored cardId instead of a charge count.
      return ($cardId === null || $val == $cardId) ? 1 : 0;
    }
    return 0;
  }

  public static function isSmokeThemOutArmed($card, $event)
  {
    return self::getSmokeThemOutCharges($card->getPId(), $card->getId()) > 0;
  }

  public static function armSmokeThemOut($pId)
  {
    $armed = Globals::getSmokeThemOutArmed();
    $armed[$pId] = self::getSmokeThemOutCharges($pId) + 1;
    Globals::setSmokeThemOutArmed($armed);
  }

  public static function consumeSmokeThemOutCharge($pId, $cardId = null)
  {
    $charges = self::getSmokeThemOutCharges($pId, $cardId);
    if ($charges <= 0) {
      return;
    }
    $armed = Globals::getSmokeThemOutArmed();
    $remaining = $charges - 1;
    if ($remaining <= 0) {
      unset($armed[$pId]);
    } else {
      $armed[$pId] = $remaining;
    }
    Globals::setSmokeThemOutArmed($armed);
  }
  
  public static function disarmSmokeThemOut($pId)
  {
    $armed = Globals::getSmokeThemOutArmed();
    unset($armed[$pId]);
    Globals::setSmokeThemOutArmed($armed);
  }
  
  /**
   * True only when LY Smoke Them Out should react to this event.
   * - Incomplete feat: react once two {D} abilities were activated on your turn.
   * - Completed+armed: react on the next {D} ability activation on your turn.
   * Regular discards (e.g. Stigma of Havoc, Sabotage) are not {D} activations.
   */
  public static function smokeThemOutLyTrigger($card, $event)
  {
    if (!self::isMe($card, $event) || !self::isMyTurn($card, $event)) {
      return false;
    }

    $isSupportDiscard = ($event['action'] ?? '') == 'Discard' && self::isSupportEffect($card, $event);
    $isSupportChooseAssignment = ($event['action'] ?? '') == 'ChooseAssignment' && self::isSupportEffect($card, $event);
    if (!$isSupportDiscard && !$isSupportChooseAssignment) {
      return false;
    }

    if (self::isThisFeatIncomplete($card, $event)) {
      return self::checkAbilityActivatedThisTurnTypeCount($card, $event, 'discard', 2);
    }

    if (!self::isSmokeThemOutArmed($card, $event)) {
      return false;
    }

    return self::checkAbilityActivatedThisTurnTypeCount($card, $event, 'discard', 1);
  }

  /**
   * True only when AX Smoke Them Out should react to this Exhaust event.
   * - Incomplete feat: react once tap ability count reaches 3 on your turn.
   * - Completed+armed: react on another tap ability (exclude this card's own tap) on your turn.
   */
  public static function smokeThemOutAxTrigger($card, $event)
  {
    if (!self::isMe($card, $event) || !self::isMyTurn($card, $event)) {
      return false;
    }

    if (self::isThisFeatIncomplete($card, $event)) {
      return self::checkAbilityActivatedThisTurnTypeCount($card, $event, 'tap', 3);
    }

    if (!self::isSmokeThemOutArmed($card, $event)) {
      return false;
    }

    if (!self::checkAbilityActivatedThisTurnTypeCount($card, $event, 'tap', 1)) {
      return false;
    }

    // Do not consume the armed effect on this card's own tap activation.
    return self::excludeSelf($card, $event);
  }

  public static function hasNoBoost($card, $event)
  {
    return self::hasBoost($card, $event, 0, 'LTE');
  }

  public static function hasBoost($card, $event, $n = 1, $op = 'GTE')
  {
    // USELESS ?? self::isMe($card, $event) &&
    $m = $card->countToken(BOOST);
    // EVENT keep information about the card previous status in case it was move to discard
    if (isset($event['boost'])) {
      $m = $event['boost'];
    }

    if ($op == 'GTE') {
      return $m >= $n;
    }
    if ($op == 'LTE') {
      return $m <= $n;
    }
    die('Unknown op for hasBoost');
  }

  public static function hasCounterOnCard($card, $event, $n = 1, $op = 'GTE')
  {
    $m = $card->getExtraDatas()['counter'] ?? 0;
    if ($op == 'GTE') {
      return $m >= $n;
    }
    if ($op == 'LTE') {
      return $m <= $n;
    }
    if ($op == 'EQ') {
      return $m == $n;
    }
    die('Unknown op for hasCounterOnCard');
  }

  public static function heroHasCounter($card, $event, $n = 1, $op = 'GTE')
  {
    return self::hasCounterOnCard($card->getPlayer()->getHero(), $event, $n, $op);
  }

  public static function hasGainedBoost($card, $event, $n = 1)
  {
    return self::hasGained($card, $event, BOOST, $n);
  }

  public static function hasGained($card, $event, $type, $n = 1)
  {
    if (($event['action'] ?? null) != GAIN) {
      return false;
    }
    if ($event['gain']['cardId'] != $card->getId()) {
      return false;
    }

    if ($event['gain']['type']  != $type) {
      return false;
    }

    return true;
  }

  public static function isGain($card, $event, $type, $n = 1)
  {
    if (($event['action'] ?? null) != GAIN) {
      return false;
    }

    if ($event['gain']['type']  != $type) {
      return false;
    }

    return true;
  }

  public static function isMyGainInReserve($card, $event)
  {
    return ($event['action'] ?? null) == GAIN && $event['location'] == RESERVE && $card->getPId() == Cards::get($event['gain']['cardId'])->getPId();
  }

  public static function isGainInStorms($card, $event)
  {
    return ($event['action'] ?? null) == GAIN && in_array($event['location'] ?? '', STORMS);
  }

  public static function isGainCardType($card, $event, $type)
  {
    return self::typeCheck($type, $event['cardType'], $event['token']);
  }

  public static function hasGainedFleeting($card, $event)
  {
    if (($event['action'] ?? null) != GAIN) {
      return false;
    }
    if (is_null($event['gain'] ?? null)) {
      return false;
    }

    if (Cards::get($event['gain']['cardId'])->getPId() != $card->getPId()) {
      return false;
    }

    if (!in_array(Cards::get($event['gain']['cardId'])->getType(), [TOKEN, CHARACTER])) {
      return false;
    }

    if ($event['gain']['type']  != FLEETING) {
      return false;
    }

    return true;
  }

  public static function hasPlayerGained($card, $event, $type)
  {
    if (($event['action'] ?? null) != GAIN) {
      return false;
    }

    if (Cards::get($event['gain']['cardId'])->getPId() != $card->getPId()) {
      return false;
    }

    if ($event['gain']['type'] != $type) {
      return false;
    }

    return true;
  }

  public static function isPlayedInSameLocation($card, $event)
  {
    return $card->getPId() == ($event['locationPId']  ?? -1) && (
      $card->getLocation() == ($event['to'] ?? '') || ($event['gigantic'] ?? false) || $card->isGigantic()
    );
  }

  public static function isNotMeInvoke($card, $event)
  {
    return $card->getPId() != ($event['locationPId']  ?? $card->getPId());
  }

  public static function isMeInvoke($card, $event)
  {
    return $card->getPId() == ($event['locationPId']  ?? $card->getPId());
  }

  public static function isNotPlayedInSameLocation($card, $event)
  {
    return $card->getLocation() != ($event['to'] ?? '');
  }

  /////////////////////////////////////////////////////////////
  //   ____  _                      _    ____              _
  //  |  _ \| | __ _ _   _  ___  __| |  / ___|__ _ _ __ __| |
  //  | |_) | |/ _` | | | |/ _ \/ _` | | |   / _` | '__/ _` |
  //  |  __/| | (_| | |_| |  __/ (_| | | |__| (_| | | | (_| |
  //  |_|   |_|\__,_|\__, |\___|\__,_|  \____\__,_|_|  \__,_|
  //                 |___/
  /////////////////////////////////////////////////////////////

  public static function isAddedCardEvent($card, $event, $playedOnly = false, $allPlayers = false)
  {
    if (!($event['playCard'] ?? false)) {
      return false;
    }

    if (!self::isMe($card, $event) && !$allPlayers) {
      return false;
    }

    // Distinguish play and put
    if ($playedOnly && ($event['putAndNotPlayed'] ?? false)) {
      return false;
    }

    return true;
  }

  public static function isExhaustedInLocation($card, $event, $location)
  {
    return ($event['cardLocation'] ?? '') == $location;
  }

  public static function isCardOfType($card, $event, $type = null)
  {
    // Type check
    if (!self::typeCheck($type, $event['cardType'], $event['token'], $event['additionalType'])) {
      return false;
    }
    if (in_array($type, SUBTYPES)) {
      if (!in_array($type, $event['cardSubtypes'])) {
        return false;
      }
    }
    return true;
  }

  public static function isCardOfTypeRobotOrToken($card, $event)
  {
    return self::isCardOfType($card, $event, ROBOT) || self::isCardOfType($card, $event, TOKEN);
  }

  public static function isCardAddedRobotOrToken($card, $event)
  {
    return self::isCardAddedAnyPlayer($card, $event, ROBOT) || self::isCardAddedAnyPlayer($card, $event, TOKEN);
  }

  public static function isCardAddedAnyPlayer($card,  $event, $type = null, $cost = null, $op = 'GTE', $excludeMyself = '', $playedOnly = false)
  {
    if (!self::isAddedCardEvent($card, $event, $playedOnly, true)) {
      return false;
    }
    $playedCard = Cards::get($event['cardId']);

    if ($event['method'] == 'MoveCard' && ($event['defect'] ?? false) == false) {
      return false;
    }

    // is played card has PID controller?
    if ($card->getPId() != $playedCard->getPId()) {
      return false;
    }

    // Exclude myself
    if ($excludeMyself == 'true' && $card->getId() == $event['cardId']) {
      return false;
    }

    // Type check
    if (!self::typeCheck($type, $event['cardType'], $event['token'], $event['additionalType'])) {
      return false;
    }

    // Subtype check
    if (in_array($type, SUBTYPES)) {
      if (!in_array($type, $playedCard->getSubtypes())) {
        return false;
      }
    }

    // Cost check
    if (!is_null($cost)) {
      $costHand = $playedCard->getCostHand();
      if ($op == 'GTE' && $costHand < $cost) {
        return false;
      }
      if ($op == 'LTE' && $costHand > $cost) {
        return false;
      }
      if ($op == 'E' && $costHand != $cost) {
        return false;
      }
    }

    return true;
  }

  public static function isCardAdded($card, $event, $type = null, $cost = null, $op = 'GTE', $excludeMyself = '', $playedOnly = false)
  {
    if (!self::isAddedCardEvent($card, $event)) {
      return false;
    }
    $playedCard = Cards::get($event['cardId']);

    // Exclude myself
    if ($excludeMyself == 'true' && $card->getId() == $event['cardId']) {
      return false;
    }

    // Type check
    if (!self::typeCheck($type, $event['cardType'], $event['token'], $event['additionalType'])) {
      return false;
    }

    // Subtype check
    if (in_array($type, SUBTYPES)) {
      if (!in_array($type, $playedCard->getSubtypes())) {
        return false;
      }
    }

    // Cost check
    if (!is_null($cost)) {
      $costHand = $playedCard->getCostHand();
      if ($op == 'GTE' && $costHand < $cost) {
        return false;
      }
      if ($op == 'LTE' && $costHand > $cost) {
        return false;
      }
      if ($op == 'E' && $costHand != $cost) {
        return false;
      }
    }

    return true;
  }

  public static function isCardPlayed($card, $event, $type, $cost = null, $op = 'GTE', $excludeMyself = '')
  {
    return self::isCardAdded($card, $event, $type, $cost, $op, $excludeMyself, true);
  }

  public static function isCardPlayedNotCharacter($card, $event)
  {
    return !self::isCardAdded($card, $event, CHARACTER);
  }

  public static function isCardPlayedWithZeroStat($card, $event)
  {
    if (!self::isCardPlayed($card, $event, CHARACTER)) {
      return false;
    }

    $playedCard = Cards::get($event['cardId']);
    $hasZero = false;
    foreach ($playedCard->getBiomes() as $biome => $value) {
      if ($value == 0) {
        return true;
      }
    }

    return false;
  }

  public static function cardPlayedCostCheck($card, $event, $cost, $check = 'base', $op = 'GTE')
  {
    if (!isset($event['cardId'])) {
      return false;
    }

    $playedCard = Cards::get($event['cardId']);
    if ($check == 'base') {
      if ($event['from'] == 'reserve') {
        $compare = $playedCard->getCostReserve();
      } else {
        $compare = $playedCard->getCostHand();
      }
    } elseif ($check == 'hand') {
      $compare = $playedCard->getCostHand();
    } else {
      $compare = $playedCard->getCostReserve();
    }
    if ($op == 'GTE') {
      return $compare >= $cost;
    }
    if ($op == 'LTE') {
      return $compare <= $cost;
    }
  }

  public static function isCharacterFromReserveNotBlocked($card, $event)
  {
    if (!self::isCardPlayed($card, $event, CHARACTER)) {
      return false;
    }
    $additionalEffect = false;

    foreach ($event['additionalEffects'] as $addEffect) {
      if ($addEffect['type'] == $event['cardType']) {
        $additionalEffect = true;
      }
    }

    return ($event['from'] == RESERVE || $additionalEffect) &&
      !Players::hasOpponentBlockingPower($card->getPlayer(), $event['to'], $card->isGigantic());
  }

  public static function isCharacterCostHigherThanCounter($card, $event)
  {
    if (!self::isCardPlayed($card, $event, CHARACTER)) {
      return false;
    }

    $cardPlayed = Cards::get($event['cardId']);
    return ($event['reallyPlayed'] ?? true) == true && $cardPlayed->getCostHand() >= ($card->getExtraDatas()['counter'] ?? 0);
  }

  public static function isReallyPlayed($card, $event)
  {
    return ($event['reallyPlayed'] ?? true) == true;
  }

  public static function specialEffect($card, $event, $effect)
  {
    return ($event['specialEffect'] ?? '') == $effect;
  }

  public static function isPlayCard($card, $event)
  {
    if (!($event['playCard'] ?? false)) {
      return false;
    }
    return true;
  }

  public static function isAddedCardAnyPlayer($card, $event, $type = null)
  {
    if (!($event['playCard'] ?? false)) {
      return false;
    }

    // Type check
    if (!self::typeCheck($type, $event['cardType'], $event['token'], $event['additionalType'])) {
      return false;
    }

    return true;
  }

  public static function isAddedCardOpponentEvent($card, $event, $type = null, $cost = null, $op = 'GTE', $excludeMyself = '', $playedOnly = false)
  {
    if (!($event['playCard'] ?? false)) {
      return false;
    }

    if (self::isMe($card, $event)) {
      return false;
    }

    // Distinguish play and put
    if ($playedOnly && ($event['putAndNotPlayed'] ?? false)) {
      return false;
    }

    $playedCard = Cards::get($event['cardId']);

    // Exclude myself
    if ($excludeMyself == 'true' && $card->getId() == $event['cardId']) {
      return false;
    }

    // Type check
    if (!self::typeCheck($type, $event['cardType'], $event['token'], $event['additionalType'])) {
      return false;
    }

    // Subtype check
    if (in_array($type, SUBTYPES)) {
      if (!in_array($type, $playedCard->getSubtypes())) {
        return false;
      }
    }

    // Cost check
    if (!is_null($cost)) {
      $costHand = $playedCard->getCostHand();
      if ($op == 'GTE' && $costHand < $cost) {
        return false;
      }
      if ($op == 'LTE' && $costHand > $cost) {
        return false;
      }
      if ($op == 'E' && $costHand != $cost) {
        return false;
      }
    }

    return true;
  }


  public static function hasMarkers($card, $event)
  {
    return Meeples::getOfType('card-' . $card->getId(), [OCEAN, FOREST, MOUNTAIN])->count() > 0;
  }


  //////////////////////////////////////////////////////////////////////////
  //  ____  _                       _          _    ____              _ 
  // |  _ \(_)___  ___ __ _ _ __ __| | ___  __| |  / ___|__ _ _ __ __| |
  // | | | | / __|/ __/ _` | '__/ _` |/ _ \/ _` | | |   / _` | '__/ _` |
  // | |_| | \__ \ (_| (_| | | | (_| |  __/ (_| | | |__| (_| | | | (_| |
  // |____/|_|___/\___\__,_|_|  \__,_|\___|\__,_|  \____\__,_|_|  \__,_|
  //////////////////////////////////////////////////////////////////////////
  // WARNING: 'Discard' has a very large meaning, any card moving and not going to the expedition or landmark is considered to being discarded

  public static function isDiscardedEvent($card, $event, $meOnly = false)
  {
    if (!($event['discardCard'] ?? false)) {
      return false;
    }

    if ($meOnly && !self::isMe($card, $event)) {
      return false;
    }

    return true;
  }

  public static function isDiscarded($card, $event, $from = null, $to = null, $type = null)
  {
    if (!self::isDiscardedEvent($card, $event)) {
      return false;
    }

    if (!is_null($from) && $from != '' && $from != $event['from']) {
      return false;
    }
    if (!is_null($to) && $to != '' && $to != $event['to']) {
      return false;
    }

    if (!is_null($type)) {
      $discardedCard = Cards::get($event['cardId']);
      if (!self::typeCheck($type, $discardedCard->getType(), $discardedCard->isToken(), $discardedCard->getAdditionalType())) {
        return false;
      }
    }

    return true;
  }

  public static function isMyselfDiscarded($card, $event, $from = null, $to = null)
  {
    return $event['cardId'] == $card->getId() && self::isDiscarded($card, $event, $from, $to);
  }

  public static function notDiscarded($card, $event)
  {
    return $event['cardId'] == $card->getId()  && ($event['to'] ?? '') == RESERVE;
  }

  public static function notDestroyed($card, $event)
  {
    return $card->getLocation() != 'destroy';
  }

  public static function notInDiscard($card, $event)
  {
    return $card->getLocation() != DISCARD_PILE;
  }

  public static function isSacrifice($card, $event, $type = null, $subType = null, $exclude = false)
  {
    if (!($event['sacrifice'] ?? false)) {
      return false;
    }

    // Type check if needed
    if (!is_null($type)) {
      $discardedCard = Cards::get($event['cardId']);
      if (!self::typeCheck($type, $discardedCard->getType(), $discardedCard->isToken(), $discardedCard->getAdditionalType())) {
        return false;
      }
    }
    if (!is_null($subType)) {
      $discardedCard = Cards::get($event['cardId']);
      if (!self::subTypeCheck($subType, $discardedCard->getSubtypes(), $exclude)) {
        return false;
      }
    }

    return true;
  }

  public static function isSacrificeCharacterPermanent($card, $event)
  {
    return self::isSacrifice($card, $event, PERMANENT) || self::isSacrifice($card, $event, CHARACTER);
  }

  public static function isSacrificed($card, $event)
  {
    return self::isSacrifice($card, $event) && self::isMyselfDiscarded($card, $event);
  }

  public static function isDiscardedType($card, $event, $type = null)
  {
    if (!is_null($type)) {
      $discardedCard = Cards::getSingle($event['cardId'], false);
      if (is_null($discardedCard)) {
        $discardedType = $event['cardType'] ?? 'notgood';
        $isToken = $event['token'] ?? false;
        $additionalType = $event['additionalType'] ??  [];
      } else {
        $discardedType = $discardedCard->getType();
        $isToken = $discardedCard->isToken();
        $additionalType = $discardedCard->getAdditionalType();
      }
      if (!self::typeCheck($type, $discardedType, $isToken, $additionalType)) {
        return false;
      }
    }
    return true;
  }

  public static function isNotToken($card, $event)
  {
    return !($event['token'] ?? false);
  }

  public static function countOwnerBoosts($card, $event, $n = 1, $op = 'GTE')
  {
    $boosts = Meeples::getFilteredQuery($card->getPId(), null, BOOST)->get()->count();
    if ($op == 'GTE') {
      return $boosts >= $n;
    } elseif ($op == 'LTE') {
      return $boosts <= $n;
    }
  }

  ///////////////////////////////////
  //   ___  _   _                   
  //  / _ \| |_| |__   ___ _ __ ___ 
  // | | | | __| '_ \ / _ \ '__/ __|
  // | |_| | |_| | | |  __/ |  \__ \
  //  \___/ \__|_| |_|\___|_|  |___/
  ///////////////////////////////////

  // Quezalcoatl
  public static function isOpponentDraw($card, $event)
  {
    return self::isNotMe($card, $event) && ($event['location'] ?? null) != MANA;
  }
  
  // Trireme Trickster (Yzmir)
  public static function isMyDraw($card, $event)
  {
    return self::isMe($card, $event) && ($event['location'] ?? null) != MANA;
  }

  public static function realResupply($card, $event)
  {
    return ($event['notResupply'] ?? false) == false;
  }

  public static function amIResupplied($card, $event)
  {
    return $event['action'] == 'Resupply' && in_array($card->getId(), $event['cardIds']);
  }

  public static function isPermanentFromTarget($card, $event)
  {
    $card = Cards::get($event['cardId']);
    return $card->getType() == PERMANENT || in_array(PERMANENT, $card->getAdditionalType());
  }

  public static function isSpellFromTarget($card, $event)
  {
    $card = Cards::get($event['cardId']);
    return $card->getType() == SPELL || in_array(SPELL, $card->getAdditionalType());
  }

  public static function isCharacterFromTarget($card, $event)
  {
    $card = Cards::get($event['cardId']);
    return in_array($card->getType(), [CHARACTER, TOKEN]);
  }

  public static function isHandEmpty($card, $event)
  {
    return $card->getPlayer()->getHand()->count() == 0;
  }

  public static function isReserveEmpty($card, $event)
  {
    return $card->getPlayer()->getReserveCards()->count() == 0;
  }

  public static function hasXExhaustedReserve($card, $event, $n, $op = 'GTE', $player = null)
  {
    $cards = 0;
    foreach (Players::getAll() as $pId => $sPlayer) {
      if (!is_null($player) && $pId != $player) {
        continue;
      }
      $cards += $sPlayer->getReserveCards()->filter(function ($c) {
        return $c->isTapped() == true;
      })->count();
    }

    if ($op == 'GTE') {
      return $cards >= $n;
    } elseif ($op == 'LTE') {
      return $cards <= $n;
    } elseif ($op == 'EQ') {
      return $cards == $n;
    }
  }

  public static function isInBiome($card, $event, $biome, $excludeMove = false)
  {
    if (is_string($excludeMove)) {
      if ($excludeMove == 'true') {
        $excludeMove = true;
      } else {
        $excludeMove = false;
      }
    }
    return $card->isGigantic() ?
      ($card->getPlayer()->isInBiome(STORM_LEFT, $biome, $excludeMove) || $card->getPlayer()->isInBiome(STORM_RIGHT, $biome, $excludeMove)) :
      $card->getPlayer()->isInBiome($card->getLocation(), $biome, $excludeMove);
  }
  public static function isNotInBiome($card, $event, $biome)
  {
    return !self::isInBiome($card, $event, $biome);
  }

  public static function allExpeditionsNotIn($card, $event, $biome, $excludeMove = false)
  {
    if (is_string($excludeMove)) {
      if ($excludeMove == 'true') {
        $excludeMove = true;
      } else {
        $excludeMove = false;
      }
    }
    return !$card->getPlayer()->isInBiome(STORM_LEFT, $biome, $excludeMove) && !$card->getPlayer()->isInBiome(STORM_RIGHT, $biome, $excludeMove);
  }

  public static function isInBiomeWithZeroStat($card, $event)
  {
    if (!self::isCardPlayedWithZeroStat($card, $event)) {
      return false;
    }

    $playedCard = Cards::get($event['cardId']);
    $hasZero = false;
    foreach ($playedCard->getBiomes() as $biome => $value) {
      if ($value == 0 && self::isInBiome($playedCard, $event, $biome)) {
        return true;
      }
    }
    return false;
  }

  public static function isTargetAsleep($card, $event)
  {
    return Cards::get($event['cardId'])->hasToken(ASLEEP);
  }

  public static function isTargetSameOwner($card, $event)
  {
    return Cards::get($event['cardId'])->getPId() == $card->getPId();
  }

  public static function isPlayedCardInBiome($card, $event, $biome)
  {
    return $card->getPlayer()->isInBiome(Cards::get($event['cardId'])->getLocation(), $biome);
  }

  public static function isDiscardedCardInBiome($card, $event, $biome)
  {
    if (!isset($event['cardId'])) {
      return false;
    }
    $discardedCard = Cards::get($event['cardId']);
    $player = $discardedCard->getPlayer();
    $from = $event['from'] ?? $event['cardFrom'] ?? '';
    if (!in_array($from, STORMS)) {
      return false;
    }

    // #198467 - Lost in the Woods interaction with Gigantic
    // After send-to-reserve, isGigantic() is often false (solo-expedition rule uses current location).
    // Use wasGigantic that was captured at target time to check if the card was gigantic at the moment of discard
    $wasGigantic = ($event['wasGigantic'] ?? false) || ($event['gigantic'] ?? false);
    if ($wasGigantic) {
      $otherExpedition = $from == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
      return $player->isInBiome($from, $biome) || $player->isInBiome($otherExpedition, $biome);
    }

    return $player->isInBiome($from, $biome);
  }

  public static function isDiscardedCardNotInBiome($card, $event, $biome)
  {
    return !self::isDiscardedCardInBiome($card, $event, $biome);
  }
  
  /**
   * Gather the Pack (pass): at least three different printed Base Costs among qualifying units in your Expeditions.
   * Printed Base Cost uses Reserve Cost if Fleeting, otherwise Hand Cost (same as targeting).
   *
   * @param string $filter 'animal' (MU) or 'character' (BR Rare: all Characters / tokens in expeditions)
   */
  public static function gatherThePackPass($card, $event, $filter = 'animal')
  {
    $player = $card->getPlayer();
    $inExpeditions = $player->getPlayedCards([CHARACTER, TOKEN])->filter(function ($c) {
      return $c->isGigantic() || in_array($c->getLocation(), STORMS);
    });
    if ($filter === 'animal') {
      $inExpeditions = $inExpeditions->filter(fn($c) => in_array(ANIMAL, $c->getSubtypes()));
    }
    $distinct = [];
    foreach ($inExpeditions as $c) {
      $printed = $c->hasToken(FLEETING) ? $c->getCostReserve() : $c->getCostHand();
      $distinct[$printed] = true;
    }
    return count($distinct) >= 3;
  }

  /**
   * Sound the Howl! (pass): at least three different printed Forest ({V}) values among Animals
   * in your Expeditions (same in-expedition scope as Gather the Pack).
   */
  public static function soundTheHowlPass($card, $event)
  {
    $player = $card->getPlayer();
    $inExpeditions = $player->getPlayedCards([CHARACTER, TOKEN])->filter(function ($c) {
      return $c->isGigantic() || in_array($c->getLocation(), STORMS);
    });
    $inExpeditions = $inExpeditions->filter(fn($c) => in_array(ANIMAL, $c->getSubtypes()));
    $distinct = [];
    foreach ($inExpeditions as $c) {
      $distinct[$c->getForest() + $c->countToken(BOOST)] = true;
    }
    return count($distinct) >= 3;
  }

  public static function XCharacterInExpedition($card, $event, $n, $op = 'GTE')
  {
    $loc = $card->getLocation();
    $nCards = $card->getPlayer()->getPlayedCards([TOKEN, CHARACTER])->filter(function ($c) use ($loc) {
      return $c->isGigantic()  || $c->getLocation() == $loc;
    })->count();
    if ($op == 'GTE') {
      return $n >= $nCards;
    } elseif ($op == 'LTE') {
      return $n <= $nCards;
    } elseif ($op == 'E') {
      return $n == $nCards;
    }
  }

  // TODO multiplayer
  public static function isOpponentExpeditionEmpty($card, $event)
  {
    return self::countOpponentExpedition($card, $event, CHARACTER) == 0;
    // $opponent = null;
    // foreach (Players::getAll() as $pId => $player) {
    //   if ($pId != $card->getPId()) {
    //     $opponent = $player;
    //   }
    // }
    // $oppositeExpedition = $card->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
    // return  $opponent->countCardsInLocation($card->getLocation(), [TOKEN, CHARACTER]) == 0 && !$opponent->hasGigantic();
  }

  /**
   * For internal use, counts the opponent's in-play cards matching $type (null = no type filter) on the
   * same storm as $card, or on the opposite storm when $card or that opponent card is gigantic.
   */
  private static function countOpponentExpedition($card, $event, $type = null)
  {
    $opponent = null;
    foreach (Players::getAll() as $pId => $player) {
      if ($pId != $card->getPId()) {
        $opponent = $player;
      }
    }
    $opponentCards = $opponent->getPlayedCards($type);
    $oppositeExpedition = $card->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;

    $n = 0;

    foreach ($opponentCards as $oId => $oCard) {
      if ($oCard->getLocation() == $card->getLocation() || ($oCard->getLocation() == $oppositeExpedition && $oCard->isGigantic()) || ($oCard->getLocation() == $oppositeExpedition && $card->isGigantic())) {
        $n++;
      }
    }
    return  $n;
  }

  /**
   * True when $cards (after optional exclusion) cover both expeditions: one in STORM_LEFT and one in
   * STORM_RIGHT, or any included card is gigantic.
   */
  private static function hasPresenceInBothStorms($cards, $excludeCardId = null)
  {
    $left = false;
    $right = false;

    foreach ($cards as $c) {
      if (!is_null($excludeCardId) && $c->getId() == $excludeCardId) {
        continue;
      }
      if ($c->isGigantic()) {
        return true;
      }
      if ($c->getLocation() == STORM_LEFT) {
        $left = true;
      }
      if ($c->getLocation() == STORM_RIGHT) {
        $right = true;
      }
      if ($left && $right) {
        return true;
      }
    }

    return false;
  }

  /**
   * Another Character (or matching $type) in each expedition — excludes $card unless $includeSelf is true.
   * Card text "Character" is implemented as CHARACTER + TOKEN (see Corrupted Gargoyle / Jeanne).
   */
  public static function controlInAllExpeditions($card, $event, $type = null, $includeSelf = 'false')
  {
    if (is_null($type) || $type === '' || $type === 'character') {
      $type = [CHARACTER, TOKEN];
    }

    $excludeCardId = $includeSelf === 'true' ? null : $card->getId();

    return self::hasPresenceInBothStorms(
      $card->getPlayer()->getPlayedCards($type),
      $excludeCardId
    );
  }

  public static function hasOtherCharacterInAllExpeditions($card, $event, $op = 'GTE')
  {
    $hasBoth = self::controlInAllExpeditions($card, $event);

    if ($op == 'LTE') {
      return !$hasBoth;
    }
    return $hasBoth;
  }

  public static function isOpponentExpeditionNotEmpty($card, $event)
  {
    return !self::isOpponentExpeditionEmpty($card, $event);
  }

  public static function isOpponentExpeditionFilled($card, $event, $type = '', $n = 1, $op = 'EQ')
  {
    if ($op == 'EQ') {
      return self::countOpponentExpedition($card, $event, $type) == $n;
    } elseif ($op == 'GTE') {
      return self::countOpponentExpedition($card, $event, $type) >= $n;
    } elseif ($op == 'LTE') {
      return self::countOpponentExpedition($card, $event, $type) <= $n;
    }
  }

  public static function isPlayedInOpponentExpedition($card, $event)
  {
    $playedCard = Cards::get($event['cardId']);
    return (($event['to'] ?? '') == $card->getLocation() || ($playedCard->isGigantic() && in_array($event['to'], STORMS)) || ($card->isGigantic() &&  in_array($event['to'], STORMS))) && $playedCard->getPId() != $card->getPId();
  }

  public static function isPlayedInOpponentOtherExp($card, $event)
  {
    $playedCard = Cards::get($event['cardId']);
    $otherExpedition = $card->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
    return ($event['to'] ?? '') == $otherExpedition && $playedCard->getPId() != $card->getPId() && Players::getNext($card->getPId())->countCardsInLocation($otherExpedition, [CHARACTER]) == 1;
  }

  public static function isPlayedInOwnOtherExpedition($card, $event)
  {
    $playedCard = Cards::get($event['cardId']);
    $otherExpedition = $card->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
    return (($event['to'] ?? '') == $otherExpedition || ($playedCard->isGigantic() && in_array($event['to'], STORMS))) && $playedCard->getPId() == $card->getPId();
  }

  public static function isNotGigantic($card, $event)
  {
    $playedCard = Cards::get($event['cardId']);
    return !$playedCard->isGigantic();
  }

  public static function isOpponentExpeditionIn($card, $event, $biome)
  {
    $opponent = null;
    foreach (Players::getAll() as $pId => $player) {
      if ($pId != $card->getPId()) {
        $opponent = $player;
      }
    }
    return $opponent->isInBiome($card->getLocation(), $biome, true) || ($card->isGigantic() && $opponent->isInBiome($card->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT, $biome, true));
  }

  public static function isCardExpeditionAscended($card, $event)
  {
    if (!in_array($card->getLocation(), STORMS)) {
      return false;
    }
    // $side = $card->getLocation() == STORM_LEFT ? HERO : COMPANION;
    return $card->getPlayer()->isAscended($card->getLocation()) || ($card->isGigantic() && ($card->getPlayer()->isAscended($card->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT)));
  }
  
  public static function wasCardExpeditionAscended($card, $event)
  {
    return $event['sourceAscended'] ?? false;
  } 

  public static function isNotCardExpeditionAscended($card, $event)
  {
    return !self::isCardExpeditionAscended($card, $event);
  }

  public static function countSourceAscended($card, $event)
  {
    return ($card->getPlayer()->isAscended(STORM_LEFT) == true ? 1 : 0) + ($card->getPlayer()->isAscended(STORM_RIGHT) == true ? 1 : 0);
  }

  public static function hasNoSourcePlayerAscended($card, $event)
  {
    return self::countSourceAscended($card, $event) == 0;
  }

  public static function hasSourcePlayerAscended($card, $event)
  {
    return self::countSourceAscended($card, $event)  > 0;
  }

  public static function hasSourcePlayerAllAscended($card, $event)
  {
    return self::countSourceAscended($card, $event) == 2;
  }

  public static function isSupportEffect($card, $event)
  {
    return ($event['isSupport'] ?? false) == true;
  }
  
  public static function hasAtLeastOneCardWithNoSupportInReserve($card, $event)
  {
    $noCardWithSupport = false;
    $reserveCards = $card->getPlayer()->getReserveCards();
    foreach ($reserveCards as $rCard) {
      if (empty($rCard->getEffectSupport())) {
        $noCardWithSupport = true;
        break;
      }
    }
    return $noCardWithSupport;
  }

  public static function isTargetForestLessOrEqualToSource($card, $event)
  {
    if (!isset($event['cardId'])) {
      return false;
    }
    $target = Cards::get($event['cardId']);
    return $target->getForest() <= $card->getForest();
  }

  public static function countMonoVisibleRegions($card, $event, $n = 1, $op = 'GTE')
  {
    $visibleRegions = Players::getRegionsInfo();
    $count = 0;
    foreach ($visibleRegions as $vId => $regions) {
      if (count($regions) == 1) {
        $count++;
      }
    }
    if ($op == 'GTE' && $count >= $n) {
      return true;
    } elseif ($op == 'LTE' && $count <= $n) {
      return true;
    }
    return false;
  }

  public static function cardInMonoRegion($card, $event)
  {
    $visibleRegions = Players::getRegionsInfo();
    $count = 0;
    $gigantic = $card->isGigantic();
    $tokenF = $card->getLocation() == STORM_LEFT ? 'getHeroToken' : 'getCompanionToken';
    $token = $card->getPlayer()->$tokenF()->getLocationArg();
    // TODO: manage gigntic?
    if (isset($visibleRegions[$token]) && count($visibleRegions[$token]) == 1) {
      return true;
    }

    // gigantic
    if ($gigantic) {
      $tokenF = $card->getLocation() == STORM_LEFT ? 'getCompanionToken' : 'getHeroToken';
      $token = $card->getPlayer()->$tokenF()->getLocationArg();
      // TODO: manage gigntic?
      if (isset($visibleRegions[$token]) && count($visibleRegions[$token]) == 1) {
        return true;
      }
    }

    return false;
  }

  /**
   * Simple Card condition checks
   */

  /**
   * Check if card is of a given type
   * @param string $type - a single card type, or a list of types separated by |
   */
  public static function isType($card, $_event, $type)
  {
    $types = explode('|', $type);
    return in_array($card->getType(), $types);
  }

  /**
   * Check subtypes of a card
   * @param string $subType - a single subtype, or a list of subtypes separated by |
   */
  public static function isSubtype($card, $_event, $subType)
  {
    $subTypes = explode('|', $subType);
    return count(array_intersect($card->getSubtypes(), $subTypes)) > 0;
  }

  public static function isTargetSubtype($card, $event, $subType)
  {
    $cardId = $event['cardId'] ?? null;
    if ($cardId === null) {
      return false;
    }
    return self::isSubtype(Cards::get($cardId), $event, $subType);
  }

  /**********************************
   **********************************
   ************* HELPERS ************
   **********************************
   *********************************/

  public static function typeCheck($type, $cardType, $isToken, $additionalType = [])
  {
    if (in_array($type, [PERMANENT, SPELL])) {
      if ($cardType != $type && !in_array($type, $additionalType)) {
        return false;
      }
      if (in_array($type, $additionalType)) {
        return true;
      }
    }

    if ($type == CHARACTER && !in_array($cardType, [CHARACTER, TOKEN])) {
      return false;
    }
    if ($type == 'characterOnly' && ($cardType != CHARACTER || $isToken)) {
      return false;
    }
    if ($type == TOKEN && !$isToken) {
      return false;
    }
    return true;
  }

  public static function subTypeCheck($subType, $subTypes, $exclude = false)
  {
    if (!$exclude) {
      if (in_array($subType, $subTypes)) {
        return true;
      }
    } elseif ($exclude) {
      if (!in_array($subType, $subTypes)) {
        return true;
      }
    }
    return false;
  }

  /******************ROLLS*******************/

  public static function hasRolled($card, $event, $result = 1, $op = 'GTE')
  {
    $rolls = $event['rolls'] ?? [];
    foreach ($rolls as $roll) {
      if ($op == 'GTE' && $roll >= $result) {
        return true;
      } elseif ($op == 'LTE' && $roll <= $result) {
        return true;
      }
    }
    return false;
  }

  public static function selectedRoll($card, $event, $result, $op = 'GTE')
  {
    $roll = $event['selectedRoll'] ?? -1;
    if ($op == 'GTE' && $roll >= $result) {
      return true;
    } elseif ($op == 'LTE' && $roll <= $result) {
      return true;
    } elseif ($op == 'EQ' && $roll == $result) {
      return true;
    }
    return false;
  }



  /**********************************
   **********************************
   **********************************
   ************* TODO ***************
   **********************************
   **********************************
   *********************************/


  public static function boostedByOtherCard($card, $event)
  {
    if ($event['sourceId'] != $card->getId() && $event['gain']['type'] == BOOST && $event['gain']['cardId'] == $card->getId()) {
      return true;
    }
    return false;
  }

  public static function isCharacterBoosted($card, $event)
  {
    return $event['pId'] == $card->getPId() &&
      $event['gain']['type'] == BOOST &&
      Cards::get($event['gain']['cardId'])->getPId() == $card->getPId();
  }

  public static function GainFirstBoost($card, $event)
  {
    return $event['pId'] == $card->getPId() &&
      $event['gain']['type'] == BOOST &&
      Cards::get($event['gain']['cardId'])->getPId() == $card->getPId() &&
      ($event['initialBoost'] ?? -1) == 0;
  }

  public static function isControlledCharacterGain($card, $event)
  {
    $event['cardId'] = $event['gain']['cardId'];
    return self::hasSameOwner($card, $event) && self::isCharacterFromTarget($card, $event);
  }

  public static function isCharacterBoostedAndUntap($card, $event)
  {
    if (!isset($event['gain'])) {
      return false;
    }
    $gainedCard = Cards::get($event['gain']['cardId']);
    return !$card->isTapped() &&
      $event['gain']['type'] == BOOST &&
      $gainedCard->getPId() == $card->getPId() &&
      in_array($gainedCard->getLocation(), STORMS);
  }

  public static function isRollEqualCostHand($card, $event)
  {
    $player = $card->getPlayer();
    $pId = $player->getId();

    $selectedRoll = $event['selectedRoll'] ?? 0;
    $draw = Cards::getInLocation("reveal-$pId");
    foreach ($draw as $dId => $drawn) {
      if ($selectedRoll == $drawn->getCostHand()) {
        return true;
      }
    }
    return false;
  }

  public static function isNonTokenBoostedAndUntap($card, $event)
  {
    $gainCard = Cards::get($event['gain']['cardId']);
    return !$card->isTapped() &&
      $event['gain']['type'] == BOOST &&
      $gainCard->getType() == CHARACTER &&
      $gainCard->isToken() == false &&
      $gainCard->getPId() == $card->getPId();
  }
}
