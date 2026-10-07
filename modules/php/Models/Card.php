<?php

namespace ALT\Models;

use ALT\Managers\Players;
use ALT\Core\Game;
use ALT\Core\Globals;
use ALT\Core\Engine;
use ALT\Managers\Meeples;
use ALT\Core\Notifications;
use ALT\Helpers\Conditions;
use ALT\Helpers\FT;
use ALT\Helpers\Utils;
use ALT\Managers\Cards;

/*
 * Card
 */

class Card extends \ALT\Helpers\DB_Model
{
  protected $implemented = true; // For DEV only

  protected $table = 'cards';
  protected $primary = 'card_id';
  protected $attributes = [
    'id' => ['card_id', 'int'],
    'location' => 'card_location',
    'state' => ['card_state', 'int'],
    'pId' => ['player_id', 'int'],

    // attributs persistants
    'properties' => ['properties', 'obj'], // will superseed original properties if needed
  ];
  protected $id;
  protected $location;
  protected $state;
  protected $pId;
  protected $properties;

  protected $staticAttributes = [];
  protected $propertiesAttributes = [
    'equinoxId' => 'int',
    'faction' => 'str',
    'name' => 'str', // obj?
    'type' => 'str', // Token/hero/adventurer/spell
    'typeline' => 'str',
    'subtypes' => 'obj',
    'token' => 'bool',
    'uid' => 'str',
    'flavorText' => 'str',
    'effectDesc' => 'str',
    'supportDesc' => 'str',
    'supportIcon' => 'str',
    'artist' => 'str',
    'setIcon' => 'str',
    'thumbnail' => 'int', // Only used for Heros for UI
    'statData' => 'int',
    'extension' => 'str',
    'fullArt' => 'bool',

    'rarity' => 'int',
    'asset' => 'str',
    'mainAsset' => 'str',
    'frame' => 'int',
    'reserveSlots' => 'int',
    'landmarkSlots' => 'int',

    'mountain' => 'int',
    'forest' => 'int',
    'ocean' => 'int',

    'costModifier' => 'obj', // ['hand'=> action check, 'reserve' => action check]
    'costHand' => 'int',
    'costReserve' => 'int',
    'costTemple' => 'int',
    'costReductionDiscard' => 'int', // to manage possibilites to discard a card, to reduce cost to pay
    'dynamicCostReduction' => 'str',

    'effectPlayed' => 'obj', // Played, no mater from hand or reserve
    'effectHand' => 'obj', // played from hand
    'effectReserve' => 'obj', // played from reserve
    'effectSupport' => 'obj',
    'effectPassive' => 'obj', // [[listener type => action]]: listener type to distinguish
    // Passive-style modifiers that apply while the Feat is completed (FEAT_COMPLETED meeple on card).
    'effectCompleted' => 'obj',
    'effectTap' => 'obj',
    'effectInfinity' => 'obj',

    'gigantic' => 'bool',
    'dynamicGigantic' => 'str',
    'fleeting' => 'bool',
    'seasoned' => 'bool',
    'minManaOrbs' => 'int', //used for cards that cannot be played unless specific amount of total mana
    'defender' => 'bool',
    'dynamicDefender' => 'str',
    'oppositeDefender' => 'bool', // OD_Common_Issitoq
    'dynamicOppositeDefender' => 'str',
    'eternal' => 'bool',
    'blockingPower' => 'bool',
    'dynamicBlockingPower' => 'str',
    'increaseOpponentCardsCost' => 'int',
    'increaseOpponentCharacterCost' => 'int',
    'increaseOpponentSpellCost' => 'int',
    'increaseOpponentPermanentCost' => 'int',
    'increaseOpponentTokenCost' => 'int',
    'opponentCharactersMinimumCost' => 'int',
    'opponentCardsMinimumCost' => 'int',
    'sacrificeAndNotFleetingGoToReserve' => 'bool',
    'sacrificeAndFleetingDraw' => 'bool',
    'updateExpeditions' => 'obj', // type = All, region []
    'blockAutomaticAction' => 'obj',
    'addDice' => 'int',
    'resupply2' => 'bool',
    'leaveExpeditionToMana' => 'bool', // Mighty Jinn
    'leaveExpeditionToManaOrDraw' => 'bool', // Mighty Jinn
    'leaveExpeditionBoostedToMana' => 'bool', // Tiny Jinn


    // Tough management
    'tough' => 'int',
    'dynamicTough' => 'str',
    'excludeUniversalTough' => 'bool',
    'excludeSelfTough' => 'bool',
    'dynamicEternal' => 'str',
    'addRoll' => 'int',

    // Dynamic info
    'tapped' => 'bool',
    'extraDatas' => 'obj',

    // Alizé
    'playTappedCards' => 'obj', // ['type'=>ALL / Character, 'location'=>me or unset]
    'playTappedCharacters' => 'bool', // for Uniques
    'playTappedAllCards' => 'bool', // for Unique
    'canAlwaysGainFleeting' => 'bool',
    'dynamicGainReplace' => 'obj',
    'defenderIgnoreBehind' => 'bool', // Ignore defender attribute when behind
    'ignoreDefender' => 'bool', // Mobile Armory
    'dynamicIgnoreDefender' => 'str', // Unique version
    'cooldown' => 'bool', // in spell cleanup, card will be tapped
    'exhaustedReserveSlots' => 'int',
    'costReductionIfEmpty' => 'int',
    'giganticOneCharacter' => 'bool', // If in only one exp, it is gigantic. Eat Me Energy Bars
    'opponentOceanOnly' => 'bool', // Will o the Wisp
    'opponentMountainOnly' => 'bool', // Will o the Wisp
    'opponentForestOnly' => 'bool', // Will o the Wisp
    'increaseBiomesHighest' => 'bool', // WinterOufits
    'advanceTwiceDusk' => 'bool', // Magic Sleigh
    'protectAnchoredInExpedition' => 'bool', // Floral tent
    'protectBoostedInExpedition' => 'bool', // Floral tent
    'increaseReserveCost' => 'int', // Ebenezer Scrooge
    'dynamicIncreaseReserveCost' => 'str',
    'reduceReserveCost' => 'int', // Ebenezer Scrooge
    'dynamicReduceReserveCost' => 'str', // Ebenezer Scrooge
    'dynamicMinimumReserveCost' => 'str', // Ebenezer Scrooge Unique
    'exhaustCharactersMorning' => 'bool', // Snow queen
    'resupplyExhaust' => 'bool', // Machine in the ice

    // Bise
    'scout' => 'int',
    'dynamicSeasoned' => 'str',
    'expeditionSeasoned' => 'bool',
    'increaseAllOtherCharactersBiomesHighest' => 'bool', // Bliss Bassist
    'ignoreReserveLimit' => 'bool', // Lyra Contortionist
    'blockOpponentReserveGain' => 'bool', // Health Inspector
    'blockGainNewCounters' => 'bool', // Health Inspector Rare
    'allReserveSlots' => 'int', // Simurgh
    'actionInsteadAdvance' => 'str', // Rune's testament
    'dynamicReserveSlots' => 'str', // Scholar's Vault
    'reduceCostType' => 'obj', // // Scholar's Vault - Rare


    // Patch note 20250729
    'allSpell1Fleeting' => 'bool', // Afanas

    // Cyclone
    'costReductionSacrificePermanent' => 'int', // Detonation
    'revealed' => 'bool', // Leviathan Observer
    'createMarkers' => 'bool', // Nadir & bubbles
    'dynamicIncreaseBiomeHighestSelf' => 'str', // Lyra Aerialist
    'costReductionLimitation' => 'int', // Lost In the riptide
    'effectPlayedLimited' => 'obj', // Lost In the riptide
    'additionalType' => 'obj', // Alelo
    'resupplyIfAscended' => 'bool', // Mandjet

    // Patch note 20251003
    'blockMoveExpedition' => 'bool', // Unique Will O the Wisp
    'reserveAdd' => 'int',

    // Duster
    'expeditionTough' => 'str',
    'playLimitation' => 'str',
    'leaveExpeditionDefect' => 'bool',
    'overrideContact' => 'bool',
    'overrideBehind' => 'bool',
    'defenderIgnoreContact' => 'bool', // Ignore defender attribute when in contact
    'costReductionTap' => 'int', // to manage possibilites to discard a card, to reduce cost to pay

    // Eole
    'playCondition' => 'str', // Conditions required to play the card
    'cantGainBoost' => 'str', // Conditions required to not gain boosts
    'boostIfAscended' => 'bool', // Wigwagging Kiwi

    // Fugue
    'costReductionIfConstructionPlayed' => 'int',
    'sacrificeProtectAnchored' => 'bool', // Fane of Calypso
    'sacrificeProtectAsleep' => 'bool', // Fane of Calypso (Ordis)
    'universalToughScope' => 'str', // Restricts a universalCharacter Tough aura, e.g. Trireme Captain
  ];

  /********* DB ACCESS *********/

  // Magic getter to test DB Field & properties field
  public function __call($method, $args)
  {
    if (preg_match('/^([gs]et|inc|is)([A-Z])(.*)$/', $method, $match)) {
      // Sanity check : does the name correspond to a declared variable ?
      $name = mb_strtolower($match[2]) . $match[3];

      // TODO put management of properties
      if (\array_key_exists($name, $this->propertiesAttributes)) {
        if ($match[1] == 'get') {
          $type = $this->propertiesAttributes[$name];
          if (isset($this->properties[$name])) {
            if (count($args) > 0 && $type == 'obj' && is_array($this->properties[$name])) {
              return $this->properties[$name][$args[0]];
            } else {
              return $this->properties[$name];
            }
          } // Chec if card is in reserve, not tapped & have infinity effect 
          elseif ($name != 'effectInfinity' && $this->getLocation() == RESERVE && !$this->isTapped() && isset($this->properties['effectInfinity'][$name])) {
            if (count($args) > 0 && $type == 'obj' && is_array($this->properties['effectInfinity'][$name])) {
              return $this->properties['effectInfinity'][$name][$args[0]];
            } else {
              return $this->properties['effectInfinity'][$name];
            }
          }
          // Default value
          else {
            if ($type == 'int') {
              return 0;
            }
            if ($type == 'bool') {
              return false;
            }
            if ($type == 'str') {
              return '';
            }
            if ($type == 'obj') {
              return [];
            }
          }
        } elseif ($match[1] == 'is') {
          if (!isset($this->properties[$name])) {
            return false;
          }
          // Boolean getter
          return (bool) $this->properties[$name];
        } elseif ($match[1] == 'set') {
          return $this->setProperty($name, $args[0]);
        }
      }
      // Default DB behavior
      else {
        return parent::__call($method, $args);
      }
    } else {
      return parent::__call($method, $args);
    }
  }

  public function getProperty($variable)
  {
    return $this->properties[$variable] ?? null;
  }

  public function setProperty($variable, $value, $updateDB = true)
  {
    $this->properties[$variable] = $value;
    if ($updateDB) {
      $this->setProperties($this->properties);
    }
  }

  public function isSupported($players, $options)
  {
    return $this->implemented;
  }

  public function getTypeStr()
  {
    return '';
  }

  public function getUiData()
  {
    // TODO: update
    return $this->jsonSerialize(); // Static datas are already in js file
  }

  public function isPlayed()
  {
    return $this->location == STORM_LEFT ||
      $this->location == STORM_RIGHT ||
      $this->location == LANDMARK ||
      $this->properties['type'] == HERO;
  }

  public function isMana()
  {
    return $this->location = 'mana';
  }

  public function getPlayer($checkPlayed = false)
  {
    if (!$this->isPlayed() && $checkPlayed) {
      throw new \feException("Trying to get the player for a non-played card : {$this->id}");
    }

    return Players::get($this->pId);
  }

  public function hasTemple()
  {
    return $this->getCostTemple() > 0;
  }

  public function isPlayedAsTemple()
  {
    return ($this->getExtraDatas()['playedAsTemple'] ?? false) === true;
  }

  public function getTemplePlayableLocations($player)
  {
    if (!$this->hasTemple() || $this->isTapped()) {
      return [];
    }

    if ($this->canBePlayed($player, false, false, true)) {
      return [LANDMARK];
    }

    return [];
  }

  // Class-file properties are reloaded each request; only DYNAMIC_PROPERTIES persist from DB.
  // Re-apply temple runtime state whenever a temple-played card is loaded.
  public function restoreTemplePlayState($updateDB = true)
  {
    if (!$this->isPlayedAsTemple()) {
      return;
    }

    $this->setProperty('type', PERMANENT, $updateDB);
    $this->setProperty('subtypes', [CONSTRUCTION, LANDMARK], $updateDB);
    // Keep the original typeline for display (e.g. "Character - Deity")
    $originalTypeline = $this->getExtraDatas()['templeOriginal']['typeline'] ?? null;
    if (!is_null($originalTypeline)) {
      $this->setProperty('typeline', $originalTypeline, $updateDB);
    }
    $this->setProperty('effectPlayed', [], $updateDB);
    $this->setProperty('effectHand', [], $updateDB);
    $this->setProperty('effectReserve', [], $updateDB);
  }

  public function applyTemplePlay()
  {
    $extra = $this->getExtraDatas();
    if (!$this->isPlayedAsTemple()) {
      $extra['playedAsTemple'] = true;
      $extra['templeOriginal'] = [
        'type' => $this->getType(),
        'subtypes' => $this->getSubtypes(),
        'typeline' => $this->getTypeline(),
        'effectPlayed' => $this->getEffectPlayed(),
        'effectHand' => $this->getEffectHand(),
        'effectReserve' => $this->getEffectReserve(),
      ];
      $this->setExtraDatas($extra);
    }
    $this->restoreTemplePlayState(true);
    // Do not refreshCard here: remounting in-place before the play/move slide
    // causes a visible flicker, and the following move notif already carries the card.
  }

  public function revertFromTemplePlay()
  {
    if (!$this->isPlayedAsTemple()) {
      return;
    }

    $extra = $this->getExtraDatas();
    $original = $extra['templeOriginal'] ?? null;
    if (!is_null($original)) {
      $this->setType($original['type']);
      $this->setSubtypes($original['subtypes']);
      $this->setTypeline($original['typeline']);
      $this->setEffectPlayed($original['effectPlayed'] ?? []);
      $this->setEffectHand($original['effectHand'] ?? []);
      $this->setEffectReserve($original['effectReserve'] ?? []);
    }

    unset($extra['playedAsTemple'], $extra['templeOriginal']);
    $this->setExtraDatas($extra);
    // Same as applyTemplePlay: the subsequent discard/move notif carries the card.
  }

  // $scout = can be played at scout cost
  // $temple = can be played at temple cost as a Landmark Construction
  public function canBePlayed($player, $scout = false, $reserveFlipCost = false, $temple = false)
  {
    if (!$temple && $this->isExhaustedReservePlayBlocked($player)) {
      return false;
    }

    if ($temple && !$this->hasTemple()) {
      return false;
    }

    if ($this->isNameBlockedThisDay($player)) {
      return false;
    }

    $playCondition = $this->getPlayCondition();
    if ($playCondition != null) {
      if (!Conditions::check(['condition' => $playCondition], $this, null)) {
        return false;
      }
    }

    $playCondition = $this->getPlayCondition();
    if ($playCondition != null) {
      if (!Conditions::check(['condition' => $playCondition], $this, null)) {
        return false;
      }
    }

    $cost = $this->getCost($scout, $reserveFlipCost, $temple);
    $costReductionIfEmpty = $this->getCostReductionIfEmpty();
    $mana = $player->getMana();
    $totalMana = $player->getTotalMana();
    // Amarok case
    if ($costReductionIfEmpty > 0) {
      if (
        ($player->countCardsInLocation(STORM_LEFT, [TOKEN, CHARACTER])  == 0 ||
          $player->countCardsInLocation(STORM_RIGHT, [TOKEN, CHARACTER]) == 0)
        && !$player->hasGigantic()
      ) {
        $cost -= $costReductionIfEmpty;
      }
    }

    $costReductionIfConstructionPlayed = $this->getCostReductionIfConstructionPlayed();
    if ($costReductionIfConstructionPlayed > 0 && $player->hasPlayedConstructionThisDay()) {
      $cost -= $costReductionIfConstructionPlayed;
    }

    if ($this->getCostReductionDiscard() > 0) {
      $reserveCards = $this->getPlayer()
        ->getReserveCards()
        ->count();
      if ($this->getLocation() == RESERVE && $reserveCards >= 2) {
        $cost -= $this->getCostReductionDiscard();
      } elseif ($reserveCards >= 1) {
        $cost -= $this->getCostReductionDiscard();
      }
    }

    if ($this->getCostReductionTap() > 0) {
      $reserveCards = $this->getPlayer()
        ->getReserveCards()->filter(function ($c) {
          return !$c->isTapped();
        })
        ->count();
      if ($this->getLocation() == RESERVE && $reserveCards >= 2) {
        $cost -= $this->getCostReductionTap();
      } elseif ($reserveCards >= 1) {
        $cost -= $this->getCostReductionTap();
      }
    }

    if ($this->getCostReductionSacrificePermanent() > 0) {
      $permanent = $this->getPlayer()->getPlayedCards(PERMANENT)->count();
      if ($permanent > 0) {
        $cost -= $this->getCostReductionSacrificePermanent();
      }
    }

    if ($this->getCostReductionLimitation() > 0) {
      $cost -= $this->getCostReductionLimitation();
    }

    // Lyra DJ
    if ($this->getPlayLimitation() == '-2Contact' && $player->isInContact()) {
      $cost -= 2;
    } elseif ($this->getPlayLimitation() == '-2Multi') {
      $opponent = Players::getNext($player);
      if ($opponent->countCardsInLocation(STORM_LEFT, CHARACTER) || $opponent->countCardsInLocation(STORM_RIGHT, CHARACTER)) {
        $cost -= 2;
      }
    }
    return $cost <= $mana && $this->getMinManaOrbs() <= $totalMana;
  }

  /**
   * Ship Security Officer (and similar): opponents can't play cards with that name this Day.
   */
  public function isNameBlockedThisDay($player)
  {
    return Players::isCardNameBlockedThisDay($player->getId(), $this->getName());
  }

  /**
   * Exhausted reserve cards are unplayable unless Vaike / Kelonic Heater / etc. allow it.
   * Used by both paid (canBePlayed) and free (ChooseAssignment) play eligibility.
   */
  public function isExhaustedReservePlayBlocked($player)
  {
    return $this->getLocation() == RESERVE
      && $this->isTapped()
      && !$player->canPlayTappedCards($this->getType(), null, $this->getAdditionalType());
  }

  public function getPlayableLocation($player, $forcedLocation = null, $free = false)
  {
    if (in_array(LANDMARK, $this->getSubtypes())) {
      // Exhausted landmarks still need Vaike (or similar) to leave Reserve.
      return $this->isExhaustedReservePlayBlocked($player) ? [] : [LANDMARK];
    } elseif ($this->getType() == SPELL) {
      return $this->isExhaustedReservePlayBlocked($player) ? [] : [LIMBO];
    } else {
      $locations = [];
      if ($this->getLocation() == RESERVE && $this->isTapped()) {
        foreach (STORMS as $storm) {
          if (($player->canPlayTappedCards($this->getType(), $storm) && is_null($forcedLocation)) ||
            ($player->canPlayTappedCards($this->getType(), $storm) && !is_null($forcedLocation) && $forcedLocation == $storm)
          ) {
            $locations[] = $storm;
          }
        }
        return $locations;
      } else {
        if ($this->getCostReductionIfEmpty() > 0) {
          // If the cost can be paid no matter what, we put all storms
          if ($this->getCost() <= $player->getMana()) {
            if (!is_null($forcedLocation)) {
              return [$forcedLocation];
            }
            return STORMS;
          }
          $locations = [];
          if ($player->countCardsInLocation(STORM_LEFT, [TOKEN, CHARACTER]) == 0) {
            if ((!is_null($forcedLocation) && $forcedLocation == STORM_LEFT) || is_null($forcedLocation)) {
              $locations[] = STORM_LEFT;
            }
          }
          if ($player->countCardsInLocation(STORM_RIGHT, [TOKEN, CHARACTER]) == 0) {
            if ((!is_null($forcedLocation) && $forcedLocation == STORM_RIGHT) || is_null($forcedLocation)) {
              $locations[] = STORM_RIGHT;
            }
          }
          return $locations;
        } elseif ($this->getPlayLimitation() == 'noOtherCardInHand') {
          // From hand: no other card in hand. From reserve: hand must be empty.
          if (!Conditions::hasXCardsInHandExceptCurrentCard($this, null, 0, 'LTE')) {
            return [];
          }
          if (!is_null($forcedLocation)) {
            return [$forcedLocation];
          }
          return STORMS;
        } elseif ($this->getPlayLimitation() == 'controlFeat') {
          if (!$player->getPlayedCards()->filter(fn($c) => in_array(FEAT, $c->getSubtypes()))->count()) {
            return [];
          }
          if (!is_null($forcedLocation)) {
            return [$forcedLocation];
          }
          return STORMS;
        } elseif ($this->getPlayLimitation() == 'nonStartingRegion') {
          $locations = [];
          if ($player->getHeroToken()->getLocation() != 'storm-0') {
            $locations[] = STORM_LEFT;
          }
          if ($player->getCompanionToken()->getLocation() != 'storm-7') {
            $locations[] = STORM_RIGHT;
          }
          return $locations;
        } elseif ($this->getPlayLimitation() == '+3StartingRegion') {
          // Diocles Chariot Racer Rare
          $locations = [];
          if ($free) {
            return STORMS;
          }
          if ($player->getHeroToken()->getLocation() == 'storm-0' && ($this->getCost() + 3) <= $player->getMana()) {
            $locations[] = STORM_LEFT;
          } elseif ($player->getHeroToken()->getLocation() != 'storm-0') {
            $locations[] = STORM_LEFT;
          }

          if ($player->getCompanionToken()->getLocation() == 'storm-7' && ($this->getCost() + 3) <= $player->getMana()) {
            $locations[] = STORM_RIGHT;
          } elseif ($player->getCompanionToken()->getLocation() != 'storm-7') {
            $locations[] = STORM_RIGHT;
          }
          return $locations;
        } else {
          if (!is_null($forcedLocation)) {
            return [$forcedLocation];
          }

          return STORMS;
        }
      }
    }
  }

  public function getScoutableLocations($player, $forcedLocation = null)
  {
    $locations = [];

    if ($this->getCostReductionIfEmpty() > 0) {
      // If the cost can be paid no matter what, we put all storms
      if ($this->getCost(true) <= $player->getMana()) {
        if (!is_null($forcedLocation)) {
          return [$forcedLocation . '_scout'];
        }
        return ['stormLeft_scout', 'stormRight_scout'];
      }
      $locations = [];
      if ($player->countCardsInLocation(STORM_LEFT, [TOKEN, CHARACTER]) == 0) {
        if ((!is_null($forcedLocation) && $forcedLocation == STORM_LEFT) || is_null($forcedLocation)) {
          $locations[] = 'stormLeft_scout';
        }
      }
      if ($player->countCardsInLocation(STORM_RIGHT, [TOKEN, CHARACTER]) == 0) {
        if ((!is_null($forcedLocation) && $forcedLocation == STORM_RIGHT) || is_null($forcedLocation)) {
          $locations[] = 'stormRight_scout';
        }
      }
      return $locations;
    } else {
      if (!is_null($forcedLocation)) {
        return [$forcedLocation . '_scout'];
      }
      return ['stormLeft_scout', 'stormRight_scout'];
    }
  }

  public function hasToken($token)
  {
    return $this->countToken($token) > 0;
  }

  public function countToken($token)
  {
    return Meeples::countMeeples('card-' . $this->id, $token);
  }

  public function hasCounters()
  {
    $tokens = $this->countToken(BOOST);
    $counters = $this->getExtraDatas()['counter'] ?? 0;
    if ($tokens > 0 || $counters > 0) {
      return true;
    }
    return false;
  }

  public function countCounters()
  {
    $tokens = $this->countToken(BOOST);
    $counters = $this->getExtraDatas()['counter'] ?? 0;
    return $tokens + $counters;
  }

  public function getOfType($type)
  {
    return Meeples::getOfType('card-' . $this->id, $type);
  }

  public function discard($seasoned = [], $gigantic = [])
  {
    return $this->discardTo(DISCARD_PILE, $seasoned, false, $gigantic);
  }

  public function moveToReserve($seasoned = [], $gigantic = [])
  {
    return $this->discardTo(RESERVE, $seasoned, false, $gigantic);
  }

  public function discardTo($location, $seasoned = [], $afterNight = false, $gigantic = [])
  {
    $isSeasoned = $this->isSeasoned();
    $this->checkLeaveListener($location, $afterNight, false, $gigantic);
    if ($this->isPlayedAsTemple()) {
      $this->revertFromTemplePlay();
    }
    $this->setLocation($location);
    $extra = $this->getExtraDatas();
    if (isset($extra['pId'])) {
      $this->setPId($extra['pId']);
      unset($extra['pId']);
      $this->setExtraDatas($extra);
    }
    $this->setTapped(false);

    // Remove meeples
    $meeples = Meeples::getInLocation('card-' . $this->id);
    if ($location == RESERVE && ($isSeasoned || in_array($this->id, $seasoned))) {
      $meeples = $meeples->filter(fn($m) => $m->getType() != BOOST); // Seasoned card keep boost
    }
    $meepleIds = $meeples->getIds();
    if (!empty($meepleIds)) {
      Meeples::delete($meepleIds);
    }
    // Clear counter
    if (!is_null($this->getExtraDatas()['counterName'] ?? null) && in_array($location,  [DISCARD_PILE, HAND, TOP_OF_DECK])) {
      $this->setExtraDatas([]);
      Notifications::deleteCounter($this);
    }

    return $meepleIds;
  }

  // deletes Token that must be removed at night
  public function nightCleanup()
  {
    $meepleIds = Meeples::getFiltered(null, 'card-' . $this->id, [ASLEEP, ANCHORED])->getIds();
    Meeples::delete($meepleIds);
    return $meepleIds;
  }

  public function getOwner()
  {
    $extra = $this->getExtraDatas()['pId'] ?? $this->getPId();
    if ($extra != $this->getPId()) {
      return $extra;
    }
    return $this->getPId();
  }

  public function checkLeaveListener($target, $afterNight, $isSacrifice = false, $gigantic = [])
  {
    $type = null;
    $location = $this->getLocation();
    if (in_array($location, STORMS)) {
      $type = 'LeaveExpedition';
    } elseif ($location == LANDMARK) {
      $type = 'LeaveLandmark';
    } else {
      $type = 'LeaveOther';
    }

    $event = [
      'type' => $type,
      'method' => $type,
      'cardId' => $this->id,
      'from' => $this->getLocation(),
      'sourceAscended' => in_array($this->getLocation(), STORMS)
        ? $this->getPlayer()->isAscended($this->getLocation()) || ($this->isGigantic() && $this->getPlayer()->isAscended($this->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT))
        : false,
      'to' => $target,
      'pId' => $this->getPId(),
      'owner' => $this->getOwner(),
      'controller' => $this->getPId(),
      'cardType' => $this->getType(),
      'additionalType' => $this->getAdditionalType(),
      'boost' => $this->countToken(BOOST),
      'fleeting' => $this->hasToken(FLEETING),
      'token' => $this->isToken(),
      'gigantic' => $this->isGigantic() || in_array($this->id, $gigantic)
    ];
    $afterCleanup = Globals::getAfterNightCleanup();
    if ($this->isListeningTo($event)) {
      $event['cardsToListen'] = [$this->id];
      if ($afterNight && !$isSacrifice) {
        $afterCleanup[$this->getPId()][] = [
          'action' => ACTIVATE_CARD,
          'args' => [
            'cardId' => $this->id,
            'event' => $event,
          ],
          'pId' => $this->getPId(),
        ];
      } elseif (!$isSacrifice) {
        Engine::pushAfterFinishingChilds([
          [
            'action' => ACTIVATE_CARD,
            'args' => [
              'cardId' => $this->id,
              'event' => $event,
            ],
            'pId' => $this->getPId(),
          ],
        ]);
      }
    }

    // Additions if landmark and is removed at night, it becames a sacrifice
    if ($isSacrifice && $afterNight) {
      $event2 = [
        'type' => 'Discard',
        'method' => 'Discard',
        'discardCard' => true,
        'cardsToListen' => [$this->id], // we add the discarded cards as they should react even if not played
        'cardId' => $this->id,
        'additionalType' => $this->getAdditionalType(),
        'token' => $this->isToken(),
        'from' => LANDMARK,
        'to' => DISCARD_PILE,
        'sacrifice' => $isSacrifice
      ];
      if ($this->isListeningTo($event2)) {
        $afterCleanup[$this->getPId()][] = [
          'action' => ACTIVATE_CARD,
          'args' => [
            'cardId' => $this->id,
            'event' => $event2,
          ],
          'pId' => $this->getPId(),
        ];
      }
    }

    // trigger reactions of listeners on onther leave
    $event['type'] = 'Other' . $event['type'];
    $event['method'] = 'Other' . $event['method'];
    $event['pId'] = $this->getPId();
    $reaction = Cards::getReaction($event);

    if ($afterNight == true) {
      if (!is_null($reaction)) {
        foreach ($reaction as $r => $reactio) {
          $afterCleanup[$reactio['pId']][] = $reactio;
        }
      }
    } else {
      Engine::pushAfterFinishingChilds($reaction);
    }
    Globals::setAfterNightCleanup($afterCleanup);
  }

  /**
   * actSupport emits Discard; ActivateEffect emits ChooseAssignment. Legacy passives may only
   * register one of the two for {D} activations (EOLE trigger 797, The Hunger, etc.).
   */
  protected function resolvePassiveEventAction($passive, $event)
  {
    $action = $event['action'] ?? 'none';
    if (isset($passive[$action])) {
      return $action;
    }
    if (($event['isSupport'] ?? false) !== true) {
      return $action;
    }
    if ($action === 'Discard' && isset($passive['ChooseAssignment'])) {
      return 'ChooseAssignment';
    }
    if ($action === 'ChooseAssignment' && isset($passive['Discard'])) {
      return 'Discard';
    }
    return $action;
  }

  /**
   * Event modifiers template
   **/
  public function isListeningTo($event)
  {
    if (!in_array($this->id, $event['cardsToListen'] ?? []) && $this->getLocation() == RESERVE && !$this->isTapped()) {
      $passive = $this->getEffectInfinity()['effectPassive'] ?? null;
      if (is_null($passive)) {
        return false;
      }
    } else {
      $passive = $this->getEffectPassive();
    }

    $action = $this->resolvePassiveEventAction($passive, $event);

    if (
      !in_array($event['type'] ?? 'none', array_keys($passive)) &&
      !in_array($action, array_keys($passive))
    ) {
      return false;
    }

    if ($event['phase'] ?? false) {
      if ($event['pId'] != $this->getPId()) {
        return false;
      }
    }

    if (isset($event['action']) && !empty($passive[$action]['listeningConditions'] ?? [])) {
      // in some rare cases, check must be done before, (like Icebound taiga)
      // var_dump(debug_print_backtrace());
      $conditions = $passive[$action]['listeningConditions'];
      foreach ($conditions as $cond) {
        $t = explode(':', $cond);
        $condFct = $t[0];
        $condArgs = array_slice($t, 1);

        if (Conditions::$condFct($this, $event, ...$condArgs) === false) {
          // var_dump(self::getName(), $cond, $event);
          return false;
        }
        // var_dump(debug_print_backtrace());
      }
    }

    return true;
  }

  /**
   * Whether the passive reaction to $event should resolve inline (within the current
   * action's branch) instead of being deferred to the after-finishing node.
   *
   * Opt-in per passive entry via `'immediate' => true`. Used e.g. by Brassbug Hive so a
   * Robot's "gains 1 boost" lands before a later same-phase effect (Sap Extractor) reads it.
   */
  public function isImmediateReaction($event)
  {
    if (
      !in_array($this->id, $event['cardsToListen'] ?? []) &&
      ($this->getLocation() == RESERVE ||
        in_array($this->id, $event['reserveToListen'] ?? []))
    ) {
      $passive = $this->getEffectInfinity()['effectPassive'] ?? null;
    } else {
      $passive = $this->getEffectPassive();
    }
    if (empty($passive)) {
      return false;
    }
    $power = $passive[$event['action'] ?? $event['type'] ?? 'none'] ?? null;
    if (is_null($power)) {
      return false;
    }
    return ($power['immediate'] ?? false) === true;
  }

  public function getReactions($event)
  {
    if (
      !in_array($this->id, $event['cardsToListen'] ?? []) &&
      ($this->getLocation() == RESERVE ||
        in_array($this->id, $event['reserveToListen'] ?? []))
    ) {
      $passive = $this->getEffectInfinity()['effectPassive'] ?? null;
      if (is_null($passive)) {
        return false;
      }
    } else {
      $passive = $this->getEffectPassive();
    }
    $effects = [];
    // manage player events?
    if (empty($passive)) {
      return [null, null];
    }

    $action = $this->resolvePassiveEventAction($passive, $event);
    if (!isset($passive[$event['type'] ?? 'none']) && !isset($passive[$action])) {
      return [null, null];
    }

    $power = $passive[$event['action'] ?? $event['type']] ?? null;
    if (is_null($power)) {
      return [null, null];
    }
    // we are on a listener with multiple effects
    if (isset($power['childs'])) {
      $parallelOutput = [];
      // looks like Payment is not used anymore
      foreach ($power['childs'] as $i => $pow) {
        list($payment, $output) = $this->checkReaction($pow, $event);
        if (!is_null($output) && !empty($output)) {
          $parallelOutput[] = $output;
        }
      }
      return [null, ['type' => NODE_PARALLEL, 'childs' => $parallelOutput, 'noIndependent' => $this->getRarity() == RARITY_UNIQUE]];
    } else {
      return $this->checkReaction($power, $event);
    }
    // $n = $power['n'] ?? 'once';
    // switch ($n) {
    //   case 'eachExpedition':
    //     $output = [];
    //     foreach (STORMS as $storm) {
    //       $event['expedition'] = $storm;
    //       if (Conditions::check($power, $this, $event) === false) {
    //         continue;
    //       }
    //       $output[] = $power['output'];
    //     }
    //     if (empty($output)) {
    //       return [null, null];
    //     }
    //     $power['output'] = FT::SEQ(...$output);
    //     break;
    //   case 'once':
    //     // structured : ['Noon'=>['condition' =>, 'output'=>]]
    //     if (Conditions::check($power, $this, $event) === false) {
    //       return [null, null];
    //     }

    //     break;
    // }

    // // put the source as the card triggering itself
    // $power['output']['sourceId'] = $this->id;

    // return [$power['payment'] ?? [], $power['output']];
  }

  protected function checkReaction($power, $event)
  {
    $n = $power['n'] ?? 'once';

    // Management of Defect power
    if (isset($power['pId'])) {
      $power['output']['pId'] = $power['pId'];
      if (isset($power['oppositeOutput'])) {
        $power['oppositeOutput']['pId'] = $power['pId'];
      }
    }

    switch ($n) {
      case 'eachExpedition':
        $output = [];
        foreach (STORMS as $storm) {
          $event['expedition'] = $storm;
          if (Conditions::check($power, $this, $event) === false) {
            if (isset($power['oppositeOutput']) && $power['oppositeOutput'] != 'OPPOSITE') {
              $output[] = $power['oppositeOutput'];
            } else {
              continue;
            }
          } else {
            $output[] = $power['output'];
          }
        }
        if (empty($output)) {
          return [null, null];
        }
        $power['output'] = FT::SEQ(...$output);
        break;
      case 'once':
        // structured : ['Noon'=>['condition' =>, 'output'=>]]
        if (Conditions::check($power, $this, $event) === false) {
          if (isset($power['oppositeOutput']) && $power['oppositeOutput'] != 'OPPOSITE') {
            $power['output'] = $power['oppositeOutput'];
          } else {
            return [null, null];
          }
        }

        break;
    }

    // put the source as the card triggering itself
    $power['output']['sourceId'] = $this->id;
    return [$power['payment'] ?? [], $power['output']];
  }

  public function getCost($scout = false, $reserveFlipCost = false, $temple = false)
  {
    if (($this->getType() == SPELL || in_array(SPELL, $this->getAdditionalType())) && Globals::isNextSpellIsFree()) {
      return 0;
    }
    $minimumCost = Players::getOpponentMinimumCost($this->getPlayer(), $this->getType());

    $minimumCost = max($minimumCost, Players::getMinimumReserveCost());

    $costReduction = Globals::getCostReduction()[$this->getPId()] ?? [];
    $typeReduction = 0;
    foreach ($costReduction as $reducType => $reduction) {
      if (!is_array($reduction)) {
        continue;
      }
      if ($reducType == $this->getType() || in_array($reducType, $this->getAdditionalType()) || $reducType == ALL) {
        $typeReduction += $reduction['reduction'];
        $minimumCost = max($minimumCost, ($reduction['minimum'] ?? 0));
      }
    }
    
    foreach ($this->getSubtypes() as $subtype) {
      if (!isset($costReduction[$subtype]) || !is_array($costReduction[$subtype])) {
        continue;
      }
      $typeReduction += $costReduction[$subtype]['reduction'];
      $minimumCost = max($minimumCost, ($costReduction[$subtype]['minimum'] ?? 0));
    }

    // TODO: to update in multiplayer
    $dynamicReduc = 0;
    $dynamicReductions = $this->getDynamicCostReduction();
    if (!is_array($dynamicReductions)) {
      $dynamicReductions = [$dynamicReductions];
    }
    foreach ($dynamicReductions as $dynamicReduction) {
      $dynSplit = explode(':', $dynamicReduction);
      if (count($dynSplit) > 1) {
        // we need to test if ok, add change dynamic tough to the value of 0
        if (!is_null(Utils::checkAttributeCondition('cost', $dynamicReduction, $this->getPlayer(), $this))) {
          $dynamicReduc += (int) $dynSplit[0];
        }
      } else {
        if ($dynamicReduction == '') {
        } elseif ($dynamicReduction == 'exhaustedReserve') {
          $cards = 0;
          foreach (Players::getAll() as $pId => $sPlayer) {
            $cards += $sPlayer->getReserveCards()->filter(function ($c) {
              return $c->isTapped() == true;
            })->count();
          }
          $dynamicReduc += $cards;
        } elseif ($dynamicReduction == 'eachOwnerAscended') {
          $dynamicReduc += ($this->getPlayer()->isAscended(STORM_LEFT) == true ? 1 : 0) + ($this->getPlayer()->isAscended(STORM_RIGHT) == true ? 1 : 0);
        } elseif ($dynamicReduction == 'playedCards') {
          $dynamicReduc += (Globals::getPlayedCards() ?? 0);
        } elseif ($dynamicReduction == 'hasPlayedCards') {
          $dynamicReduc += (Globals::getPlayedCards() ?? 0) > 0;
        } elseif ($dynamicReduction == 'hasInContact') {
          $dynamicReduc += $this->getPlayer()->isInContact() ? 1 : 0;
        } elseif ($dynamicReduction == 'expeditionsInContact') {
          $dynamicReduc += $this->getPlayer()->isInContact(STORM_LEFT) ? 1 : 0;
          $dynamicReduc += $this->getPlayer()->isInContact(STORM_RIGHT) ? 1 : 0;
        } else {
          $dynamicReduc += (int) $dynamicReduction;
        }
      }
    }

    $increaseReserveCost = Players::getIncreaseReserveCost($this->getType());
    $reduceReserveCost = Players::getReduceReserveCost($this->getType(), $this->getSubtypes(), $this->getPId(), $this->id);
    if ($reduceReserveCost > 0 && $this->getLocation() == RESERVE) {
      $minimumCost = max(1, $minimumCost);
    }

    // Scholar's Vault, Reka Welder (reduceCostType minimum floor)
    $reduceCostTypeData = $this->getPlayer()->getReduceCostType($this);
    $minimumCost = max($minimumCost, $reduceCostTypeData['minimum'] ?? 0);
    $dynamicReduc = (int) $dynamicReduc + $reduceCostTypeData['reduction'];

    if ($temple && $this->hasTemple()) {
      return max($minimumCost, $this->getCostTemple() - $typeReduction - (int) $dynamicReduc);
    }
    
    switch ($this->getLocation()) {
      case HAND:
        if ($scout && $this->getScout() > 0) {
          $initialCost = $this->getScout();
        } else {
          $initialCost = $this->getCostHand();
        }
        return max($minimumCost, $initialCost - $typeReduction  - (int) $dynamicReduc);
        break;
      case RESERVE:
        if ($reserveFlipCost) {
          return min(
            max($minimumCost, $this->getCostReserve() - $typeReduction - (int) $dynamicReduc + $increaseReserveCost - $reduceReserveCost),
            max($minimumCost, $this->getCostHand() - $typeReduction  - (int) $dynamicReduc + $increaseReserveCost - $reduceReserveCost)
          );
        }
        return max($minimumCost, $this->getCostReserve() - $typeReduction - (int) $dynamicReduc + $increaseReserveCost - $reduceReserveCost);
        break;
    }
  }

  public function getBiomes($includeModifiers = false, $increaseBiomesToHighest = false)
  {
    $biomes = [OCEAN => $this->getOcean(), MOUNTAIN => $this->getMountain(), FOREST => $this->getForest()];
    $dynamicIncreaseSelf = 0;
    if ($includeModifiers === true) {
      // BOOST
      $boost = $this->countToken(BOOST);
      foreach ($biomes as $type => &$value) {
        $value += $boost;
      }

      $dynamicIncrease = $this->getDynamicIncreaseBiomeHighestSelf();
      $dynSplit = explode(':', $dynamicIncrease);

      if (count($dynSplit) > 1) {
        // we need to test if ok, add change dynamic tough to the value of 0
        if (!is_null(Utils::checkAttributeCondition('cost', $dynamicIncrease, $this->getPlayer(), $this))) {
          $dynamicIncreaseSelf = (int) $dynSplit[0];
        } else {
          $dynamicIncreaseSelf = 0;
        }
      }
    }
    if ($increaseBiomesToHighest == true || $dynamicIncreaseSelf == 1) {
      $max = 0;
      foreach ($biomes as $type => $value2) {
        $max = max($max, $value2);
      }
      $biomes = [OCEAN => $max, MOUNTAIN => $max, FOREST => $max];
    }
    return $biomes;
  }

  /**
   * Completed-feat {@see effectCompleted} <code>dynamicTough</code> is defined on Gather the Pack (Rare), Delay the Collapse, etc.
   * Stored {@see subtypes} can omit {@see FEAT} (it is in {@see DYNAMIC_PROPERTIES} and may be overwritten by DB rows); still treat
   * a Landmark with a non-empty completed <code>dynamicTough</code> as eligible so expedition Tough auras keep working.
   */
  public function mayMergeCompletedFeatDynamicTough(): bool
  {
    if (in_array(FEAT, $this->getSubtypes())) {
      return true;
    }
    if (!in_array(LANDMARK, $this->getSubtypes())) {
      return false;
    }
    $completed = $this->properties['effectCompleted'] ?? [];
    if (!is_array($completed)) {
      return false;
    }
    $extra = $completed['dynamicTough'] ?? null;
    return $extra !== null && $extra !== '';
  }

  /**
   * Merges {@see effectCompleted} <code>dynamicTough</code> while the Feat is completed (same idea as landmark-only completed auras).
   */
  public function getDynamicTough()
  {
    $base = $this->properties['dynamicTough'] ?? '';

    if (!$this->mayMergeCompletedFeatDynamicTough()) {
      return $base;
    }
    if (Meeples::countMeeples('card-' . $this->getId(), FEAT_COMPLETED) < 1) {
      return $base;
    }
    $completed = $this->properties['effectCompleted'] ?? [];
    if (!is_array($completed)) {
      return $base;
    }
    $extra = $completed['dynamicTough'] ?? null;
    if ($extra === null || $extra === '') {
      return $base;
    }

    $toList = function ($v) {
      if ($v === '' || $v === null) {
        return [];
      }
      return is_array($v) ? $v : [$v];
    };

    $merged = array_merge($toList($base), $toList($extra));
    if (count($merged) === 0) {
      return '';
    }
    if (count($merged) === 1) {
      return $merged[0];
    }
    return $merged;
  }
  public function getTough()
  {
    // Tough impacts only a card in Storms or landmark
    if (!in_array($this->getLocation(), STORMS) && $this->getLocation() != LANDMARK) {
      return 0;
    }

    $tough = $this->properties['tough'] ?? 0;
    $dynamicTough = $this->getDynamicTough();
    if (!is_array($dynamicTough) && $dynamicTough != '') {
      $dynamicTough = [$dynamicTough];
    }
    if ($dynamicTough != '') {
      foreach ($dynamicTough as $singleTough) {
        $dynSplit = explode(':', $singleTough);
        if (count($dynSplit) > 1) {
          // we need to test if ok, add change dynamic tough to the value of 0
          if (!is_null(Utils::checkAttributeCondition('tough', $singleTough, $this->getPlayer(), $this))) {
            $singleTough = $dynSplit[0];
          }
        }
        switch ($singleTough) {
          case '':
            // no dynamic
            break;
          case 'region':
            if (Globals::isTieBreakerMode()) {
              break;
            }
            $diff = $this->getPlayer()->getRegionDifference() - 1;
            if ($diff >= 1) {
              $tough += $diff;
            }
            break;
          case 'controlledPlants':
            foreach ($this->getPlayer()->getPlayedCards() as $cId => $card) {
              if (in_array(PLANT, $card->getSubtypes())) {
                $tough++;
              }
            }
            break;
          case 'tough1':
            $tough += 1;
            break;
          case 'tough2':
            $tough += 2;
            break;
          case 'exhaustedReserve':
            foreach (Players::getAll() as $pId => $sPlayer) {
              $tough += $sPlayer->getReserveCards()->filter(function ($c) {
                return $c->isTapped() == true;
              })->count();
            }
            break;
        }
      }
    }

    if (in_array($this->getType(), [CHARACTER, TOKEN])) {
      $universal = $this->getPlayer()->countUniversalCharacterTough($this);
      if ($this->getExcludeUniversalTough() && $singleTough == 'universalCharacter2') {
        $universal = $universal - 2;
      }
      if ($this->getExcludeUniversalTough() && $singleTough == 'universalCharacter1') {
        $universal = $universal - 1;
      }
      $tough += $universal;

      // Rider's Mask
       if (
        ($this->getPlayer()->hasExpeditionToughBoosted($this->getLocation())
          || ($this->isGigantic() && $this->getPlayer()->hasExpeditionToughBoosted($this->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT)))
        && self::hasToken(BOOST)
      ) {
        $tough += 1;
      }

      $anchoredAsleep = $this->getPlayer()->countUniversalToughAnchoredAsleep();
      if ($anchoredAsleep > 0 && ($this->hasToken(ANCHORED) || $this->hasToken(ASLEEP))) {
        $tough += 1;
      }
      
      if ($this->hasToken(ANCHORED)) {
        $tough += $this->getPlayer()->countUniversalToughAnchored();
      }
      if ($this->hasToken(FLEETING)) {
        $tough += $this->getPlayer()->countUniversalToughFleeting();
      }
    }

    if (in_array($this->getType(), [PERMANENT])) {
      $universal = $this->getPlayer()->countUniversalLandmarksToughFromCompletedFeat();
      if ($this->getExcludeUniversalTough()) {
        $completed = $this->getEffectCompleted();
        if (
          !empty($completed) &&
          isset($completed['dynamicTough']) &&
          str_starts_with($completed['dynamicTough'], 'universalLandmarks')
        ) {
          $value = (int) str_replace(
            'universalLandmarks',
            '',
            $completed['dynamicTough']
          );
          $universal -= $value;
        } 
      }
      $tough += $universal;
    }

    // Global Tough
    $globalTough = Globals::getGlobalTough();
    if (isset($globalTough[$this->pId])) {
      foreach ($globalTough[$this->pId] as $i => $gTough) {
        if ($this->getType() == $gTough['type'] && $gTough['minHandCost'] <= $this->getCostHand()) {
          $tough += $gTough['tough'];
        }
      }
    }

    return $tough;
  }

  public function isSeasoned()
  {
    if (($this->properties['seasoned'] ?? false) == true) {
      return true;
    }

    if (in_array($this->getType(), [TOKEN, CHARACTER])) {
      if ($this->getPlayer()->hasExpeditionSeasoned($this->getLocation())) {
        return true;
      } elseif ($this->isGigantic() && $this->getPlayer()->hasExpeditionSeasoned()) {
      } elseif ($this->isSeasonedFromOppositeSourceCharacter()) {
        return true;
      }
    }
    return false;
  }

  /**
   * Characters facing an opponent with dynamicSeasoned "oppositeSourceCharacter" are seasoned.
   */
  public function isSeasonedFromOppositeSourceCharacter()
  {
    if (!in_array($this->getLocation(), STORMS)) {
      return false;
    }

    $opponent = Players::getNext($this->getPlayer());
    foreach ($opponent->getPlayedCards([CHARACTER, TOKEN]) as $sourceCard) {
      if (!in_array($sourceCard->getLocation(), STORMS)) {
        continue;
      }

      $dynamicSeasoned = $sourceCard->getDynamicSeasoned();
      if (!is_array($dynamicSeasoned) && $dynamicSeasoned != '') {
        $dynamicSeasoned = [$dynamicSeasoned];
      } elseif ($dynamicSeasoned == '') {
        continue;
      }

      foreach ($dynamicSeasoned as $singleSeasoned) {
        if (Utils::checkAttributeCondition('dynamicSeasoned', $singleSeasoned, $sourceCard->getPlayer(), $sourceCard) !== 'oppositeSourceCharacter') {
          continue;
        }
        if (Conditions::isFacingSource($this, ['cardId' => $sourceCard->getId()])) {
          return true;
        }
      }
    }

    return false;
  }

  public function isGigantic($ignoreOneGigantic = false)
  {
    if (($this->properties['gigantic'] ?? false) == true) {
      return true;
    }

    if ($this->isToken() && $this->getPlayer()->countUniversalTokenGigantic() > 0) {
      return true;
    }

    if (
      in_array(COMPANION, $this->getSubtypes())
      && in_array($this->getLocation(), STORMS)
      && $this->getPlayer()->countUniversalCompanionGigantic() > 0
    ) {
      return true;
    }

    if (in_array($this->getType(), [TOKEN, CHARACTER])) {

      $dynamicGigantic = $this->getDynamicGigantic();
      if (!is_array($dynamicGigantic) && $dynamicGigantic != '') {
        $dynamicGigantic = [$dynamicGigantic];
      } elseif ($dynamicGigantic == '') {
        $dynamicGigantic = [];
      }

      foreach ($dynamicGigantic as $singleGigantic) {
        $dynSplit = explode(':', $singleGigantic);
        if (count($dynSplit) > 1) {
          // we need to test if ok, add change dynamic tough to the value of 0
          if ($dynSplit[0] != 'universalGiganticToken' && !is_null(Utils::checkAttributeCondition('gigantic', $singleGigantic, $this->getPlayer(), $this))) {
            return $dynSplit[0];
          }
        } elseif ($singleGigantic == '1') {
          return true;
        }
      }


      $characterCount = $this->getPlayer()->countCardsInLocation($this->getLocation(), [TOKEN, CHARACTER]);
      if ($characterCount > 1 || $ignoreOneGigantic) {
        return false;
      }

      $oneCharacterGigantic = false;
      foreach ($this->getPlayer()->getPlayedCards([PERMANENT]) as $cId => $card) {
        if ($card->isGiganticOneCharacter() && $card->getLocation() == $this->getLocation()) {
          $oneCharacterGigantic = true;
        }
      }

      // Additional check to see if we do not have a gigantic character in the other expedition
      if ($oneCharacterGigantic) {
        $otherExpedition = $this->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
        foreach ($this->getPlayer()->getPlayedCards([TOKEN, CHARACTER]) as $cId => $card) {
          if ($card->getLocation() != $otherExpedition) {
            continue;
          }
          if ($card->isGigantic(true)) {
            return false;
          }
          if ($card->getState() > $this->getState()) {
            // if the card has been played after this one we do not check it
            continue;
          }
          if ($card->isGigantic()) {
            return false;
          }
        }
        return true;
      }
    }

    return false;
  }

  public function getReserveSlots()
  {
    if (($this->properties['reserveSlots'] ?? false) !== false) {
      return $this->properties['reserveSlots'];
    }
    $dynamicSlot = $this->getDynamicReserveSlots();
    if ($dynamicSlot != '') {
      $result = Utils::checkAttributeCondition('reserveSlots', $dynamicSlot, $this->getPlayer(), $this);
      if (is_null($result)) {
        return 0;
      }
      return intval($result);
    }
    return 0;
  }

  public function isOppositeDefender()
  {
    if (($this->properties['oppositeDefender'] ?? false) == true) {
      return true;
    }

    $dynamicBlocking = $this->getDynamicOppositeDefender();
    if ($dynamicBlocking != '') {
      return !is_null(Utils::checkAttributeCondition('oppositeDefender', $dynamicBlocking, $this->getPlayer(), $this));
    }
    return false;
  }

  public function getIncreaseReserveCost($type = null)
  {
    if (($this->properties['increaseReserveCost'] ?? 0) > 0) {
      return $this->properties['increaseReserveCost'];
    }

    $dynamicBlocking = $this->getDynamicIncreaseReserveCost();
    if ($dynamicBlocking != '') {
      $result = Utils::checkAttributeCondition('oppositeDefender', $dynamicBlocking, $this->getPlayer(), $this);
      if ($result == 'character' && $type == CHARACTER) {
        return 1;
      } elseif ($result == 'character') {
        return 0;
      } elseif (!is_null($result)) {
        return $result;
      }
    }
    return 0;
  }

  public function getReduceReserveCost($type, $subtypes, $ownerId, $cardId)
  {
    if (($this->properties['reduceReserveCost'] ?? 0) > 0) {
      return $this->properties['reduceReserveCost'];
    }
    if ($cardId == $this->id) {
      return 0;
    }
    $dynamicBlocking = $this->getDynamicReduceReserveCost();
    if ($dynamicBlocking != '') {
      $result = Utils::checkAttributeCondition('reduceReserveCost', $dynamicBlocking, $this->getPlayer(), $this);
      if ($result == 'myCharacter') {
        if ($type == CHARACTER && $ownerId == $this->getPId()) {
          return 1;
        } else {
          return 0;
        }
      } elseif ($result == 'myArtist' && $ownerId == $this->getPId()) {
        if (in_array(ARTIST, $subtypes)) {
          return 1;
        } else {
          return 0;
        }
      } elseif ($result == 'myRobot' && $ownerId == $this->getPId()) {
        if (in_array(ROBOT, $subtypes)) {
          return 1;
        } else {
          return 0;
        }
      } elseif ($result == "1") {
        return 1;
      } elseif (!is_null($result) && is_int($result)) {
        return $result;
      }
    }
    return 0;
  }

  public function getMinimumReserveCost()
  {
    if (($this->properties['minimumReserveCost'] ?? 0) > 0) {
      return $this->properties['minimumReserveCost'];
    }

    $dynamicBlocking = $this->getDynamicMinimumReserveCost();
    if ($dynamicBlocking != '') {
      $result = Utils::checkAttributeCondition('minimumReserveCost', $dynamicBlocking, $this->getPlayer(), $this);
      if ($result == "1") {
        return 1;
      } elseif (!is_null($result) && is_int($result)) {
        return $result;
      }
    }
    return 0;
  }

  public function isIgnoreDefender()
  {
    if (($this->properties['ignoreDefender'] ?? false) == true) {
      return true;
    }

    $dynamicBlocking = $this->getDynamicIgnoreDefender();
    if ($dynamicBlocking != '') {
      return !is_null(Utils::checkAttributeCondition('ignoreDefender', $dynamicBlocking, $this->getPlayer(), $this));
    }
    return false;
  }

  // public function getDynamicGigantic()
  // {
  //   return Utils::checkAttributeCondition('eternal', ($this->properties['dynamicGigantic'] ?? ''), $this->getPlayer(), $this);
  // }

  public function isBlockingPower()
  {
    $blocking = $this->properties['blockingPower'] ?? false;
    if ($blocking === true) {
      return true;
    }

    $dynamicBlocking = $this->getDynamicBlockingPower();
    if ($dynamicBlocking != '') {
      return !is_null(Utils::checkAttributeCondition('eternal', $dynamicBlocking, $this->getPlayer(), $this));
    }
    return false;
  }


  public function isEternal()
  {
    if (($this->properties['eternal'] ?? false) == true) {
      return true;
    }

    $dynamicEternal = $this->getDynamicEternal();
    if ($dynamicEternal != '') {
      return !is_null(Utils::checkAttributeCondition('eternal', $dynamicEternal, $this->getPlayer(), $this));
    }
    return false;
  }

  public function isDefender()
  {
    if (($this->properties['defender'] ?? false) == true) {
      return true;
    }
    $subType = '';
    $dynamicDefender = $this->getDynamicDefender();
    switch ($dynamicDefender) {
      case '2OtherPlants':
        $subType = PLANT;
        break;
      case '2OtherBureaucrats':
        $subType = BUREAUCRAT;
        break;
      case 'fullDefender':
        return true;
        break;
    }

    if ($subType != '') {
      $c = 0;
      foreach ($this->getPlayer()->getPlayedCards() as $cId => $card) {
        if ($cId == $this->id) {
          continue;
        }

        if (in_array($subType, $card->getSubtypes())) {
          $c++;
        }
      }
      if ($c < 2) {
        return true;
      }
    }

    if ($dynamicDefender != '' && $subType == '') {
      return !is_null(Utils::checkAttributeCondition('defender', $dynamicDefender, $this->getPlayer(), $this));
    }

    // Aura: "Characters in your other Expedition are Defender."
    if (in_array($this->getType(), [CHARACTER, TOKEN]) && in_array($this->getLocation(), STORMS)) {
      foreach ($this->getPlayer()->getPlayedCards()->where('location', STORMS) as $auraCard) {
        if ($auraCard->getId() == $this->getId()) {
          continue;
        }
        if ($auraCard->getDynamicDefender() != 'otherExpeditionCharacter') {
          continue;
        }
        // Gigantic cards have no "other expedition" to grant.
        if ($auraCard->isGigantic()) {
          continue;
        }
        if ($auraCard->getLocation() != $this->getLocation()) {
          return true;
        }
      }
    }

    // OD_Common_GulrangTocsin
    if (
      in_array(
        $this->getPlayer()
          ->getHero()
          ->getUid(),
        ['ALT_CORE_B_OR_03_C']
      ) &&
      $this->getPlayer()->getTotalMana() < 8
    ) {
      return $this->isToken() && $this->hasToken(BOOST);
    }
    return false;
  }

  public function isInAscended()
  {
    if (!in_array($this->getLocation(), STORMS)) {
      return false;
    }

    // $side = $this->getLocation() == STORM_LEFT ? HERO : COMPANION;
    $otherSide = $this->getLocation() == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;

    if ($this->isGigantic()) {
      return $this->getPlayer()->isAscended($this->getLocation()) || $this->getPlayer()->isAscended($otherSide);
    } else {
      return $this->getPlayer()->isAscended($this->getLocation());
    }
  }
}
