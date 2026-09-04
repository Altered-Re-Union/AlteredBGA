<?php

namespace ALT\Managers;

use ALT\Core\Globals;
use ALT\Core\Game;
use ALT\Core\Notifications;
use ALT\Helpers\FlowConvertor;
use ALT\Helpers\Utils;
use ALT\Core\Engine;
use ALT\Managers\Players;
use ALT\Models\Card;

/* Class to manage all the cards for Altered */

function slugify($text)
{
  $text = str_replace('ō', 'o', $text);
  $text = str_replace('ö', 'o', $text);
  $text = str_replace('ā', 'a', $text);
  $text = str_replace('ä', 'a', $text);
  $text = preg_replace('~[^\pL\d]+~u', '', $text);
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  $text = trim($text, '');
  if (empty($text)) {
    return 'n-a';
  }
  return $text;
}

class Cards extends \ALT\Helpers\CachedPieces
{
  use \ALT\States\SetupTrait; // temp for API

  protected static $table = 'cards';
  protected static $prefix = 'card_';
  protected static $customFields = ['player_id', 'properties'];
  protected static $autoIncrement = true;
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = true;
  protected static $autoreshuffleCustom = ['deck' => 'discard'];
  protected static $autoreshuffleListener = ['obj' => 'ALT\Managers\Cards', 'method' => 'shuffleDeck'];
  protected static $datas = null;

  protected static function cast($card)
  {
    return self::getCardInstance($card['card_id'], $card);
  }

  public static function getCardInstance($id, $data = null)
  {
    $rarities = [
      RARITY_COMMON => 'Common',
      RARITY_RARE => 'Rare',
      RARITY_UNIQUE => 'Unique',
      RARITY_EXALTED => 'Exalted',
    ];
    $p = json_decode($data['properties'], true);
    $faction = $p['faction'];
    $rarity = $rarities[$p['rarity']] ?? 'Common';

    $slug = slugify($p['name']);
    $className = '\\ALT\\Cards\\' . $faction . '\\' . $faction . '_' . $rarity . '_' . $slug;

    $isUnique = $p['rarity'] == RARITY_UNIQUE;
    // Unique => all infos are stored into DB
    if ($isUnique) {
      return new Card($data); // information from DB
    }
    // Non-unique => take non-dynamic properties from files
    else {
      if (class_exists($className)) {
        $card = new $className($data); // no DB call

        $prop = json_decode($data['properties'], true);
        // Update dynamic properties
        foreach (DYNAMIC_PROPERTIES as $p) {
          $v = $prop[$p] ?? null;
          if (!is_null($v)) {
            $card->setProperty($p, $v, false);
          }
        }
        return $card;
      }
    }
  }

  public static function isKS($uid)
  {
    return explode('_', $uid)[1] == 'COREKS';
  }

  public static function isAlternateArt($uid)
  {
    return explode('_', $uid)[2] == 'A' || in_array(explode('_', $uid)[1],  ['DUSTERTOP', 'DUSTERCB', 'DUSTEROP', 'TCS3', 'WCS25', 'MUSUBI']) || explode("_", $uid)[2] == 'P';
  }

  public static function getNextPlayedState()
  {
    $max = -1;
    $played = self::getFiltered(null, IN_PLAY);
    foreach ($played as $cId => $card) {
      $max = max($max, $card->getState());
    }
    return $max;
  }

  public static function getAltUid($uid)
  {
    $expUid = explode('_', $uid);
    $expUid[1] = 'COREKS';
    if (count($expUid) == 7) {
      unset($expUid[6]);
    }
    unset($expUid[5]);
    $altUid = implode('_', $expUid);
    return $altUid;
  }

  public static function getCoreUid($uid)
  {
    $expUid = explode('_', $uid);
    $expUid[1] = 'CORE';
    $coreUid = implode('_', $expUid);
    return $coreUid;
  }

  public static function getMainUid($uid)
  {
    $expUid = explode('_', $uid);
    if (in_array($expUid[1], ['DUSTEROP', 'DUSTERCB', 'DUSTERTOP'])) {
      if ($expUid[4] < 25) {
        $expUid[1] = 'CORE';
      } elseif ($expUid[4] < 45) {
        $expUid[1] = 'ALIZE';
      } else {
        $expUid[1] = 'DUSTER';
      }
    } elseif (in_array($expUid[1], ['TCS3'])) {
      $expUid[1] = 'BISE';
    } elseif (in_array($expUid[1], ['WCQ25', 'WCS25', 'MUSUBI'])) {
      $expUid[1] = 'CORE';
    }
    $expUid[2] = 'B';
    $coreUid = implode('_', $expUid);

    return $coreUid;
  }

  public static function getAlternateUid($uid)
  {
    $expUid = explode('_', $uid);
    if (count($expUid) == 7) {
      unset($expUid[6]);
    }
    unset($expUid[5]);
    $altUid = implode('_', $expUid);
    return $altUid;
  }

  public static function getIconSet($uid)
  {
    $expUid = explode('_', $uid);
    switch ($expUid[1]) {
      case 'COREKS':
        return 'ks';
      case 'ALIZE':
        return 'tbf';
      case 'BISE':
        return 'wfm';
      case 'CYCLONE':
        return 'so';
      case 'DUSTER':
        return 'sdu';
      case 'EOLE':
        return 'roc';  
      default:
        return null;
    }
  }

  public static function getCardClass($uid)
  {
    require_once dirname(__FILE__) . '/../Cards/cards.inc.php';
    // Mapping done for heroes for example
    if (isset(UID_MAPPING[$uid])) {
      $uid = UID_MAPPING[$uid];
    }

    $ks = self::isKS($uid);
    $alternate = self::isAlternateArt($uid);
    $altUid = '';
    $coreUid = '';
    if ($ks) {
      $coreUid = self::getCoreUid($uid);
      $altUid = self::getAltUid($uid);
    } elseif ($alternate) {
      $coreUid = self::getMainUid($uid);
      $altUid = self::getAlternateUid($uid);
    } else {
      $coreeUid = 'toto';
    }

    if (!isset(MAP_REFS_CLASSES[$uid])) {
      if (!isset(MAP_REFS_CLASSES[$coreUid])) {
        throw new \BgaVisibleSystemException('This card is not implemented ' . $uid . ' ' . $coreUid . ' ' . $altUid . 't');
      } elseif ($ks || $alternate) {
        $uid = $coreUid;
      }
    }

    $cInfo = explode('/', MAP_REFS_CLASSES[$uid]);
    $className = "\\ALT\\Cards\\$cInfo[0]\\$cInfo[1]";
    $iconSet = self::getIconSet($uid);
    $row = null;

    $cardO = new $className($row);
    if (!is_null($iconSet)) {
      $cardO->setSetIcon($iconSet);
    }

    if ($ks || $alternate) {
      if (isset(self::getAltArt()[$altUid])) {
        $altArt = self::getAltArt()[$altUid];
        $cardO->setFlavorText($altArt['flavorText']);
        $unsuffixedAltUid = $altUid;
        if ($cardO->getRarity() == RARITY_RARE) {
          $cardO->setAsset($altUid . '_R');
          $altUid .= '_R';
        } elseif ($cardO->getRarity() == RARITY_COMMON) {
          $cardO->setAsset($altUid . '_C');
          $altUid .= '_C';
        } elseif ($cardO->getRarity() == RARITY_EXALTED) {
          $cardO->setAsset($altUid . '_E');
          $altUid .= '_E';
        } else {
          $cardO->setAsset($altUid . '_U');
          $altUid .= '_U';
        }

        if (isset(self::getAltArt()[$unsuffixedAltUid]['mainAsset'])) {
          $cardO->setMainAsset(self::getAltArt()[$unsuffixedAltUid]['mainAsset']);
        } elseif (isset(self::getAltArt()[$unsuffixedAltUid]['framedBase'])) {
          $cardO->setMainAsset(self::getAltArt()[$unsuffixedAltUid]['framedBase'] . substr($altUid, strlen($unsuffixedAltUid)));
        } elseif (isset($altArt['fullArt'])) {
          $cardO->setMainAsset(self::getMainUid($unsuffixedAltUid) . substr($altUid, strlen($unsuffixedAltUid)));
        } else {
          $cardO->setMainAsset($altUid);
        }

        if (isset($altArt['fullArt'])) {
          $cardO->setFullArt(true);
        }
      }
    }
    return $cardO;
    // return new $className($row);
  }

  public static function getFiltered($pId, $location = null, $type = null, $additionalType = false)
  {
    return self::getSelectWhere(null, $location, null)
      ->where('pId', $pId)
      ->whereType('type', $type, $additionalType);
  }

  public static function generateRandomDeck($deck, $player, $faction = null)
  {
    $deckContent = self::buildRandomDeckContent($faction);
    return self::createDeck($player, $deckContent);
  }

  public static function buildRandomDeckContent($faction = null)
  {
    require_once dirname(__FILE__) . '/../Cards/cards.inc.php';

    if (is_null($faction) || $faction === '' || $faction === 'ALL') {
      $faction = FACTIONS[array_rand(FACTIONS)];
    }
    if ($faction === 'OR') {
      $faction = FACTION_OD;
    }
    if (!in_array($faction, FACTIONS, true)) {
      throw new \BgaUserException(clienttranslate('Invalid faction for random deck'));
    }
    $deckContent = [];

    $deckContent[HERO] = [
      'card' => Cards::getCardClass(HEROES[$faction][array_rand(HEROES[$faction])])->jsonSerialize(),
      'n' => 1,
    ];
    // random cards of the faction
    $i = 0;
    $totalCards = 45;
    $repartition = ['' => 2, 'TBF' => 3, 'WFTM' => 4, 'SO' => 5, 'SDU' => 6, 'ROC' => 30];
    $allocation = array_fill_keys(array_keys($repartition), 0);

    $attempts = 0;
    $maxAttempts = 20000;
    do {
      if (++$attempts > $maxAttempts) {
        throw new \feException('Could not generate a random deck: not enough implemented cards available.');
      }

      $c = array_rand(MAP_REFS_CLASSES);
      $cInfo = explode('/', MAP_REFS_CLASSES[$c]);
      $classFile = dirname(__FILE__) . '/../Cards/' . $cInfo[0] . '/' . $cInfo[1] . '.php';
      if (!file_exists($classFile)) {
        continue;
      }

      try {
        $objCard = self::getCardClass($c);
      } catch (\Throwable $e) {
        continue;
      }

      if ($objCard->getFaction() == $faction && $objCard->getType() != HERO && !$objCard->isToken()) {
        $extension = $objCard->getExtension();
        if (!isset($repartition[$extension])) {
          continue;
        }
        if ($allocation[$extension] < $repartition[$extension]) {
          $deckContent[] = ['card' => $objCard->jsonSerialize(), 'n' => 1];
          $allocation[$extension]++;
          $i++;
        }
      }
    } while ($i < $totalCards);

    $uniqueCount = 0; //increase for randomly generated unique. Warning: Most of it will be useless due to no check on trigrams
    $eoleTrigramCount = (int) ceil($uniqueCount * 0.75);
    $eoleTrigramFlags = array_merge(
      array_fill(0, $eoleTrigramCount, true),
      array_fill(0, $uniqueCount - $eoleTrigramCount, false)
    );
    shuffle($eoleTrigramFlags);

    for ($u = 0; $u < $uniqueCount; $u++) {
      $deckContent[] = [
        'card' => ['properties' => self::generateRandomUnique($faction, $eoleTrigramFlags[$u])],
        'n' => 1,
      ];
    }

    return $deckContent;
  }

  public static function generateUnique($unique)
  {
    // throw new \feException(print_r($unique));
    $properties = [];
    $properties['uid'] = $unique['reference'];
    $uid = $properties['uid'];
    $properties['rarity'] = RARITY_UNIQUE;

    $asset = explode('_', $unique['reference']);
    unset($asset[count($asset) - 1]);
    $properties['asset'] = implode('_', $asset);
    if (self::isKS($uid)) {
      $properties['setIcon'] = 'ks';

      $altUid = self::getAltUid($uid);
      // ALT_COREKS_B_YZ_04_4451
      if (isset(self::getAltArt()[$altUid])) {
        $properties['flavorText'] = self::getAltArt()[$altUid]['flavorText'];
        $properties['asset']  = $altUid . '_U';
      } else {
        $properties['asset']  = self::getCoreUid($altUid) . '_U';
      }
    }
    $properties['faction'] = Utils::convertFaction($unique['mainFaction']['reference']);
    $properties['name'] = $unique['name'];
    // $properties['type'] = constant($unique['cardType']['reference']);
    $properties['type'] = constant($unique['cardType']['reference']);


    $subtypes = [];
    $typeline = [];

    // old
    // foreach ($unique['cardSubTypes'] ?? [] as $v => $sub) {
    //   // ?? [] is temp!
    //   $subtypes[] = constant($sub['reference']);
    //   $typeline[] = $sub['name'];
    // }
    foreach ($unique['subTypes'] ?? [] as $v => $sub) {
      // ?? [] is temp!
      $subtypes[] = constant($sub);
    }   
    $properties['subtypes'] = $subtypes;
    $properties['typeline'] = $unique['typeline'];
    // $properties['artist'] = $unique['illustrator']['nickName']; old
    $properties['artist'] = $unique['illustrator'];

    $properties['costHand'] = (int) $unique['elements']['MAIN_COST'];
    $properties['costReserve'] = (int) $unique['elements']['RECALL_COST'];
    $properties['forest'] = (int) $unique['elements']['FOREST_POWER'];
    $properties['mountain'] = (int) $unique['elements']['MOUNTAIN_POWER'];
    $properties['ocean'] = (int) $unique['elements']['OCEAN_POWER'];

    // add effects
    $properties['uEffects'] = [];
    foreach ($unique['uniqueReduced'] as $i => $cardElement) {
      // throw new \feException(print_r($cardElement));
      $trinity = [];
      // throw new \feException(print_r($cardElement));
      foreach ($cardElement['effects'] as $i => $idGd) {
        if (in_array($idGd, TRIGGER)) {
          $trinity['trigger'] = $idGd;
        } elseif (in_array($idGd, \CONDITION)) {
          $trinity['condition'] = $idGd;
        } elseif (in_array($idGd, OUTPUT)) {
          $trinity['output'] = $idGd;
        }
      }
      if (empty($trinity)) {
        continue;
      }
      if (count($trinity) != 3) {
        // throw new \feException(print_r($effect));
        return null;
      }
      // var_dump($trinity);
      $valid = FlowConvertor::constructEffect($trinity, $properties);
      // var_dump($properties);
      // throw new \feException(print_r($trinity));
      $properties['uEffects'][] = array_values($trinity);
      // throw new \feException(print_r($properties));
    }
    // $debug[0] = $unique;
    // $debug[1] = $properties;
    // throw new \feException(print_r($properties));
    return $properties;
  }

  public static function parseTrigramTriggerId(string $trigger): int
  {
    $zoneTriggers = ['H' => 22, 'R' => 1, 'J' => 24, 'P' => 24];
    $upper = strtoupper(trim($trigger));
    if (isset($zoneTriggers[$upper])) {
      return $zoneTriggers[$upper];
    }
    return (int) $trigger;
  }

  /**
   * @param array<int, array{trigger: int|string, condition: int|string, output: int|string}> $trigrams
   */
  public static function generateUniqueFromTrigrams($faction, array $trigrams)
  {
    require_once dirname(__FILE__) . '/../Cards/cards.inc.php';

    $found = false;
    $cardO = null;
    $cardList = array_keys(MAP_REFS_CLASSES);
    do {
      $card = $cardList[array_rand($cardList)];
      $cardO = self::getCardClass($card);

      if ($cardO->getFaction() != $faction || $cardO->getType() != CHARACTER || $cardO->getRarity() != RARITY_COMMON) {
        continue;
      } else {
       }
      $found = true;
    } while (!$found);
    $card = $cardO->jsonSerialize()['properties'];
    $card['rarity'] = RARITY_UNIQUE;
    $card['asset'] = substr($card['asset'], 0, strlen($card['asset']) - 1) . 'U';
    foreach (
      [
        'effectDesc',
        'supportDesc',
        'supportIcon',
        'effectHand',
        'effectReserve',
        'effectPlayed',
        'effectPassive',
        'gigantic',
        'defender',
        'oppositeDefender',
        'eternal',
        'dynamicDefender',
        'dynamicTough',
        'tough',
      ]
      as $eff
    ) {
      if (isset($card[$eff])) {
        unset($card[$eff]);
      }
    }
    
    $card['uEffects'] = [];
    foreach ($trigrams as $trigram) {
      $trinity = [
        'trigger' => self::parseTrigramTriggerId((string) $trigram['trigger']),
        'condition' => (int) $trigram['condition'],
        'output' => (int) $trigram['output'],
      ];
      FlowConvertor::constructEffect($trinity, $card);
      $card['uEffects'][] = array_values($trinity);
    }

    return $card;
  }

  public static function generateUniqueFromTrigram($faction, $trigger, $condition, $output)
  {
    return self::generateUniqueFromTrigrams($faction, [[
      'trigger' => $trigger,
      'condition' => $condition,
      'output' => $output,
    ]]);
  }

  /**
   * @param bool $requireEoleComponent When true, at least one of trigger / condition / output is from EOLE.
   */
  public static function generateRandomUnique($faction, $requireEoleComponent = false)
  {
    $trinity = self::pickRandomEffectTrinity($requireEoleComponent);
    return self::generateUniqueFromTrigram(
      $faction,
      $trinity['trigger'],
      $trinity['condition'],
      $trinity['output']
    );
  }

  /**
   * Picks a random trigger / condition / output triplet for generated Uniques.
   *
   * @param bool $requireEoleComponent At least one slot uses an EOLE effect; other slots may mix EOLE and legacy.
   */
  private static function pickRandomEffectTrinity(bool $requireEoleComponent): array
  {
    static $legacyTrigger = null;
    static $legacyCondition = null;
    static $legacyOutput = null;

    if ($legacyTrigger === null) {
      $legacyTrigger = array_values(array_diff(TRIGGER, TRIGGER_LASTSET));
      $legacyCondition = array_values(array_diff(CONDITION, CONDITION_LASTSET));
      $legacyOutput = array_values(array_diff(OUTPUT, OUTPUT_LASTSET));
    }

    $slots = ['trigger', 'condition', 'output'];
    $allPools = ['trigger' => TRIGGER, 'condition' => CONDITION, 'output' => OUTPUT];
    $eolePools = ['trigger' => TRIGGER_LASTSET, 'condition' => CONDITION_LASTSET, 'output' => OUTPUT_LASTSET];
    $legacyPools = ['trigger' => $legacyTrigger, 'condition' => $legacyCondition, 'output' => $legacyOutput];

    if (!$requireEoleComponent) {
      return [
        'trigger' => $legacyPools['trigger'][array_rand($legacyPools['trigger'])],
        'condition' => $legacyPools['condition'][array_rand($legacyPools['condition'])],
        'output' => $legacyPools['output'][array_rand($legacyPools['output'])],
      ];
    }

    $eoleSlot = $slots[array_rand($slots)];
    $trinity = [];
    foreach ($slots as $slot) {
      $pool = $slot === $eoleSlot ? $eolePools[$slot] : $allPools[$slot];
      $trinity[$slot] = $pool[array_rand($pool)];
    }

    return $trinity;
  }

  public static function getUiData($pId, $refresh = false)
  {
    $current = Players::getCurrent() == null ? false : Players::getCurrent()->getId() == $pId;
    $currentPId = Players::getCurrent() == null ? -1 : Players::getCurrent()->getId();
    $cards = self::getAll()
      ->where('location', IN_PLAY)
      ->merge(self::getInLocation(RESERVE))
      ->merge(self::getInLocation('board-hero-%'))
      ->merge(self::getInLocation('limbo'))
      ->merge(self::getInLocation('discard'))
      ->merge(self::getInLocation('reveal-%'));

    if (!$refresh && $current) {
      $cards = $cards->merge(self::getHand($pId))->merge(self::getFiltered($pId, MANA));
    }
    $cards = $cards->merge(
      self::getAll()->where('location', HAND)
        ->filter(function ($c) use ($currentPId) {
          return $c->getPId() != $currentPId && $c->isRevealed();
        })
    );

    return $cards->orderBy('state')->toArray();
  }

  ///////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  ///////////////////////////////////

  public static function setupPrecoDeck($player, $deckNumber, $deckList)
  {
    // Load list of cards
    require_once dirname(__FILE__) . '/../Cards/cards.inc.php';

    $toCreate = [];
    $pId = $player->getId();

    foreach (STARTER_DECKS as $deck) {
      
      $faction = $deck['faction'];
      $deckId = $deck['deckId'];

      foreach ($deck['contents'] as $cardId => $n) {
        $factionSub = substr($cardId, 0, 2);
        $className = "\\ALT\\Cards\\$factionSub\\$cardId";
        $card = new $className(null);
        $location = "deck-" . $deckNumber;
        if ($card->getType() == HERO) {
          $deckList[$deckNumber] = ['deckNumber' => $deckNumber, 'deckId' => $deckId, 'faction' => $faction];
        }

        // we do not create token as they will be created on the fly
        if ($card->isToken()) {
          continue;
        }

        $toCreate[] = [
          'player_id' => $pId,
          'location' => $location,
          'nbr' => $n,
          'properties' => [
            'rarity' => $card->getRarity(),
            'name' => $card->getName(),
            'faction' => $card->getFaction(),
          ],
        ];
      }
      $deckNumber++;
    }

    self::create($toCreate, null);
    return $deckList;
  }

  public static function setupDemoDeck($player, $deckNumber, $deckList)
  {
    // Load list of cards
    require_once dirname(__FILE__) . '/../Cards/cards.inc.php';

    $toCreate = [];
    $pId = $player->getId();

    foreach (DEMO_ROC_DECKS as $deck) {
      $faction = $deck['faction'];
      $deckId = $deck['deckId'];

      foreach ($deck['contents'] as $cardId => $n) {
        $factionSub = substr($cardId, 0, 2);
        $className = "\\ALT\\Cards\\$factionSub\\$cardId";
        $card = new $className(null);
        $location = "deck-" . $deckNumber;
        if ($card->getType() == HERO) {
          $deckList[$deckNumber] = ['deckNumber' => $deckNumber, 'deckId' => $deckId, 'faction' => $faction];
        }

        // we do not create token as they will be created on the fly
        if ($card->isToken()) {
          continue;
        }

        $toCreate[] = [
          'player_id' => $pId,
          'location' => $location,
          'nbr' => $n,
          'properties' => [
            'rarity' => $card->getRarity(),
            'name' => $card->getName(),
            'faction' => $card->getFaction(),
          ],
        ];
      }
      $deckNumber++;
    }

    self::create($toCreate, null);
    return $deckList;
  }

  public static function createDeck($player, $deckContent)
  {
    // Load list of cards
    $toCreate = [];
    $pId = $player->getId();
    $faction = '';

    $toCreate[] = [
      'player_id' => $pId,
      'location' => 'board-hero-' . $pId,
      'nbr' => 1,
      'properties' => $deckContent[HERO]['card']['properties'],
    ];
    $faction = $deckContent[HERO]['card']['properties']['faction'];
    $location = 'deck-API';
    foreach ($deckContent as $cardInfo) {
      $card = new Card($cardInfo['card']);

      // we do not create token as they will be created on the fly
      if ($card->isToken()) {
        continue;
      }

      $toCreate[] = [
        'player_id' => $pId,
        'location' => $location,
        'nbr' => $cardInfo['n'],
        'properties' => $card->getProperties(),
      ];
      // $faction = $card->getFaction();
    }
    self::create($toCreate, null);

    return $faction;
  }

  public static function shuffleDeck($location)
  {
    Engine::checkpoint();
    $player = Players::get(explode('-', $location)[1]);
    Notifications::shuffleDeck($player, $location, self::countInLocation($location));
    Notifications::refreshUI(Game::get()->localGetAllDatas(true));
  }

  public static function pickForLocation($nbr, $fromLocation, $toLocation, $state = 0, $deckReform = false)
  {
    return parent::pickForLocation($nbr, $fromLocation, $toLocation, $state, $deckReform);
  }

  /**
   * Get all cards played by player matching the given type
   */
  public static function getPlayedCards($pId, $type = null)
  {
    return self::getFiltered($pId, IN_PLAY, $type)->orderBy('state', 'ASC');
  }

  public static function getReserveCards($pId, $type = null)
  {
    return self::getFiltered($pId, RESERVE, $type);
  }

  public static function getStormCards($pId, $type = null)
  {
    return self::getFiltered($pId, 'storm%', $type);
  }

  public static function getHand($pId, $type = null)
  {
    return self::getFiltered($pId, 'hand', $type)->orderBy('state', 'ASC');
  }

  public static function getManaChoice($pId)
  {
    return self::getFiltered($pId, 'choice')->orderBy('state', 'ASC');
  }

  /**
   * Check whether a player played a specific card
   */
  public static function hasPlayedCard($pId, $id)
  {
    $card = self::getSingle($id, false);
    return !is_null($card) && $card->isPlayed() && $card->getPId() == $pId;
  }

  public static function discard($cardIds, $discard = 'discard', $pId = null)
  {
    if (!is_null($pId)) {
      $discard = $discard . '_' . $pId;
    }
    return self::move($cardIds, $discard);
  }

  // Used during new day
  public static function untapAll()
  {
    $untapped = [];
    $exhaustedCharactersMorning = Players::isExhaustedCharactersMorning();
    foreach (self::getAll() as $cId => $card) {
      if (!is_null($card) && $card->isTapped()) {
        if (
          !$exhaustedCharactersMorning ||
          ($exhaustedCharactersMorning && (!in_array($card->getType(), [TOKEN, CHARACTER]) || $card->getLocation() != RESERVE))
        ) {
          $card->setTapped(false);
          $untapped[] = $cId;
        }
      }
    }
    Notifications::untap($untapped);
  }

  ///////////////////////////////////////
  //  _____                 _
  // | ____|_   _____ _ __ | |_ ___
  // |  _| \ \ / / _ \ '_ \| __/ __|
  // | |___ \ V /  __/ | | | |_\__ \
  // |_____| \_/ \___|_| |_|\__|___/
  ///////////////////////////////////////

  /**
   * Get all the cards triggered by an event
   */
  public static function getListeningCards($event)
  {
    $cards = self::getListeningCardsObject()
      ->filter(function ($card) use ($event) {
        return $card->isListeningTo($event);
      })
      ->getIds();

    // if we force other cards to be listened to
    if (isset($event['cardsToListen'])) {
      $cards = array_merge(
        $cards,
        Cards::getMany($event['cardsToListen'], false)
          ->filter(function ($card) use ($event, $cards) {
            return !in_array($card->getId(), $cards) && $card->isListeningTo($event);
          })
          ->getIds()
      );
    }

    if (isset($event['reserveToListen'])) {
      $cards = array_merge(
        $cards,
        Cards::getMany($event['reserveToListen'], false)
          ->filter(function ($card) use ($event, $cards) {
            return !in_array($card->getId(), $cards) && $card->isListeningTo($event);
          })
          ->getIds()
      );
    }

    return $cards;
  }

  public static function getListeningCardsObject()
  {
    return self::getInLocation(STORM_LEFT)
      ->merge(self::getInLocation(STORM_RIGHT))
      ->merge(self::getInLocation(LANDMARK))
      ->merge(self::getInLocation(RESERVE))
      ->merge(self::getInLocation('board-hero%'));
  }

  /**
   * Get reaction in form of an ARRAY of node that can be used to activate a card
   */
  public static function getReaction($event, $returnNullIfEmpty = true, $ownerPId = true)
  {
    $listeningCards = self::getListeningCards($event);
    if (empty($listeningCards) && $returnNullIfEmpty) {
      return null;
    }
    // throw new \feException(print_r($listeningCards));
    $childs = [];
    $backupEvent = $event;
    foreach ($listeningCards as $cardId) {
      $event = $backupEvent;
      $listenCard = self::get($cardId);
      if ($listenCard->getLocation() == RESERVE) {
        $event['reserveToListen'][] = $cardId;
      }
      // #147483: "Unique lyra - Timing limbo effect/cleanup
      if (isset($event['action']) && in_array($listenCard, STORMS) && ($listenCard->getEffectPassive()[$event['action']]['forceListening'] ?? false) == true) {
        $event['cardsToListen'] = array_merge($event['cardsToListen'] ?? [], [$cardId]);
      }
      $event['sourceLocation'] = self::get($cardId)->getLocation();
      $child = [
        'action' => ACTIVATE_CARD,
        // 'pId' => $event['pId'],
        'pId' => $ownerPId == true ? self::get($cardId)->getPId() : $event['pId'],
        'args' => [
          'cardId' => $cardId,
          'event' => $event,
        ],
        'sourceId' => $cardId
      ];
      if ($listenCard->isImmediateReaction($event)) {
        $child['immediate'] = true;
      }
      $childs[] = $child;
    }
    if (empty($childs) && $returnNullIfEmpty) {
      return null;
    }
    // return [
    //   'type' => NODE_PARALLEL,
    //   'pId' => $event['pId'],
    //   'childs' => $childs,
    // ];
    return $childs;
  }

  public static function getAltArt()
  {
    return [
      'ALT_COREKS_B_AX_07' => [
        'flavorText' => clienttranslate('These nameless workers are crucial to the Foundry\'s day-to-day operations.'),
      ],
      'ALT_COREKS_B_AX_18' => ['flavorText' => clienttranslate('"Down from the skies, I come to check your rage."')],
      'ALT_COREKS_B_BR_07' => ['flavorText' => clienttranslate('"Gotta go fast!"')],
      'ALT_COREKS_B_BR_22' => [
        'flavorText' => clienttranslate(
          'To attract good fortune, spend new coin on an old friend, share an old pleasure with a new friend, and lift up the heart of a true friend by writing his name on the wings of a dragon.'
        ),
      ],
      'ALT_COREKS_B_LY_09' => ['flavorText' => clienttranslate('"Never trust the Tanuki twice." - Lyra proverb')],
      'ALT_COREKS_B_LY_21' => ['flavorText' => clienttranslate('"My fourth Unique card? No idea what you\'re talking about."')],
      'ALT_COREKS_B_MU_08' => ['flavorText' => clienttranslate('Little fella\'s got so mushroom in his heart.')],
      'ALT_COREKS_B_MU_09' => [
        'flavorText' => clienttranslate('We look on with benevolent eyes, not knowing what its future holds.'),
      ],
      'ALT_COREKS_B_OR_20' => ['flavorText' => clienttranslate('A soul for a soul.')],
      'ALT_COREKS_B_OR_21' => [
        'flavorText' => clienttranslate(
          '"I think you have forgotten something. We keep a merry inn here in the greenwood, but whoever becomes our guest must pay his reckoning."'
        ),
      ],
      'ALT_COREKS_B_YZ_11' => ['flavorText' => clienttranslate('The real scarecrow is not who you think.')],
      'ALT_COREKS_B_YZ_17' => ['flavorText' => clienttranslate('In the abyssal depths, no one can hear you scream.')],
      // Alizé
      'ALT_ALIZE_A_AX_35' => ['flavorText' => clienttranslate('Even over 50 years after her death, her pioneering research in the use of kelon is mentioned in all the history books.')],
      'ALT_ALIZE_A_BR_37' => ['flavorText' => clienttranslate('"Every scar marks a defeat, and every sword is a trophy."')],
      'ALT_ALIZE_A_LY_34' => ['flavorText' => clienttranslate('"This trick was a triumph. It’s hard to overstate my satisfaction."')],
      'ALT_ALIZE_A_MU_35' => ['flavorText' => clienttranslate('"This will be a fine addition to my collection."')],
      'ALT_ALIZE_A_OR_38' => ['flavorText' => clienttranslate('"Day 28. The intense cold is putting the troops\' morale to the test. I hope the dawn will bring a bit of warmth."')],
      'ALT_ALIZE_A_YZ_36' => ['flavorText' => clienttranslate('She used her body as a rampart until she was carried away by the waves.')],
      'ALT_ALIZE_A_AX_46' => ['flavorText' => ''],
      'ALT_ALIZE_A_BR_46' => ['flavorText' => ''],
      'ALT_ALIZE_A_LY_45' => ['flavorText' => ''],
      'ALT_ALIZE_A_MU_45' => ['flavorText' => ''],
      'ALT_ALIZE_A_OR_47' => ['flavorText' => ''],
      'ALT_ALIZE_A_YZ_46' => ['flavorText' => ''],
      'ALT_CORE_A_AX_22' => ['flavorText' => ''],
      'ALT_CORE_A_BR_26' => ['flavorText' => ''],
      'ALT_CORE_A_LY_22' => ['flavorText' => ''],
      'ALT_CORE_A_MU_25' => ['flavorText' => ''],
      'ALT_CORE_A_OR_24' => ['flavorText' => ''],
      'ALT_CORE_A_YZ_19' => ['flavorText' => ''],
      'ALT_DUSTEROP_A_AX_98' => ['flavorText' => ''],
      'ALT_DUSTEROP_A_BR_99' => ['flavorText' => ''],
      'ALT_DUSTEROP_A_LY_100' => ['flavorText' => ''],
      'ALT_DUSTEROP_A_MU_98' => ['flavorText' => ''],
      'ALT_DUSTEROP_A_OR_98' => ['flavorText' => ''],
      'ALT_DUSTEROP_A_YZ_97' => ['flavorText' => ''],
      'ALT_BISE_A_AX_63' => ['flavorText' => ''],
      'ALT_BISE_A_BR_63' => ['flavorText' => ''],
      'ALT_BISE_A_LY_63' => ['flavorText' => ''],
      'ALT_BISE_A_MU_62' => ['flavorText' => ''],
      'ALT_BISE_A_OR_63' => ['flavorText' => ''],
      'ALT_BISE_A_YZ_62' => ['flavorText' => ''],
      'ALT_BISE_A_AX_56' => ['flavorText' => ''],
      'ALT_BISE_A_BR_58' => ['flavorText' => ''],
      'ALT_BISE_A_LY_53' => ['flavorText' => ''],
      'ALT_BISE_A_MU_58' => ['flavorText' => ''],
      'ALT_BISE_A_OR_57' => ['flavorText' => ''],
      'ALT_BISE_A_YZ_52' => ['flavorText' => ''],
      'ALT_CYCLONE_A_AX_74' => ['flavorText' => ''],
      'ALT_CYCLONE_A_BR_77' => ['flavorText' => ''],
      'ALT_CYCLONE_A_LY_74' => ['flavorText' => ''],
      'ALT_CYCLONE_A_MU_74' => ['flavorText' => ''],
      'ALT_CYCLONE_A_OR_74' => ['flavorText' => ''],
      'ALT_CYCLONE_A_YZ_73' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_AX_93' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_AX_97' => ['flavorText' => ''],
      'ALT_TCS3_P_AX_53' => ['flavorText' => ''],
      'ALT_TCS3_P_BR_61' => ['flavorText' => ''],
      'ALT_TCS3_P_LY_49' => ['flavorText' => ''],
      'ALT_TCS3_P_MU_51' => ['flavorText' => ''],
      'ALT_TCS3_P_OR_54' => ['flavorText' => ''],
      'ALT_TCS3_P_YZ_59' => ['flavorText' => ''],
      'ALT_WCS25_P_AX_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_AX_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_AX_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_BR_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_BR_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_BR_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_LY_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_LY_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_LY_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_MU_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_MU_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_MU_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_OR_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_OR_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_OR_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_YZ_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_YZ_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_WCS25_P_YZ_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_AX_04' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_AX_20' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_BR_19' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_BR_30' => ['flavorText' => '', 'fullArt' => true, 'framedBase' => 'ALT_CORE_B_BR_30'],
      'ALT_DUSTERTOP_P_LY_07' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_LY_04' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_MU_13' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_MU_12' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_OR_14' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_OR_08' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_YZ_06' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_YZ_12' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_AX_41' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_AX_32' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_BR_32' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_BR_38' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_LY_31' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_LY_39' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_MU_44' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_MU_33' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_OR_42' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_OR_43' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_YZ_41' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERTOP_P_YZ_44' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_AX_100' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_AX_101' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_LY_102' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_MU_100' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_AX_87' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_AX_91' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_AX_95' => ['flavorText' => '', 'fullArt' => true, 'mainAsset' => 'ALT_DUSTERCB_P_AX_95_E_F'],
      'ALT_DUSTERCB_P_BR_88' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_BR_89' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_BR_91' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_BR_96' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_BR_98' => ['flavorText' => '', 'fullArt' => true, 'mainAsset' => 'ALT_DUSTERCB_P_BR_98_E_F'],
      'ALT_DUSTERCB_P_LY_89' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_LY_90' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_LY_93' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_LY_98' => ['flavorText' => '', 'fullArt' => true, 'mainAsset' => 'ALT_DUSTERCB_P_LY_98_E_F'],
      'ALT_DUSTERCB_P_MU_86' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_MU_88' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_MU_96' => ['flavorText' => '', 'fullArt' => true, 'mainAsset' => 'ALT_DUSTERCB_P_MU_96_E_F'],
      'ALT_DUSTERCB_P_MU_99' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_OR_89' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_OR_90' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_OR_92' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_OR_93' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_OR_97' => ['flavorText' => '', 'fullArt' => true, 'mainAsset' => 'ALT_DUSTERCB_P_OR_97_E_F'],
      'ALT_DUSTERCB_P_YZ_88' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_YZ_92' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_YZ_94' => ['flavorText' => '', 'fullArt' => true, 'mainAsset' => 'ALT_DUSTERCB_P_YZ_94_E_F'],
      'ALT_DUSTERCB_P_YZ_96' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_YZ_98' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_A_AX_95' => ['flavorText' => ''],
      'ALT_DUSTER_A_BR_98' => ['flavorText' => ''],
      'ALT_DUSTER_A_LY_98' => ['flavorText' => ''],
      'ALT_DUSTER_A_MU_96' => ['flavorText' => ''],
      'ALT_DUSTER_A_OR_97' => ['flavorText' => ''],
      'ALT_DUSTER_A_YZ_94' => ['flavorText' => ''],
      'ALT_DUSTERCB_P_AX_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_BR_01' => ['flavorText' => ''],
      'ALT_DUSTERCB_P_LY_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_MU_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_OR_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTERCB_P_YZ_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_A_AX_85' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_A_BR_65' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_A_LY_65' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_A_MU_85' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_A_OR_85' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_A_YZ_65' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_P_AX_85' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_P_BR_65' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_P_LY_65' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_P_MU_85' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_P_OR_85' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTER_P_YZ_65' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_AX_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_AX_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_AX_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_BR_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_BR_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_BR_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_LY_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_LY_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_LY_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_MU_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_MU_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_MU_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_OR_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_OR_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_OR_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_YZ_01' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_YZ_02' => ['flavorText' => '', 'fullArt' => true],
      'ALT_CORE_P_YZ_03' => ['flavorText' => '', 'fullArt' => true],
      'ALT_DUSTEROP_P_BR_94' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_BR_95' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_LY_87' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_LY_94' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_MU_89' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_MU_94' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_OR_100' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_OR_101' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_OR_86' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_YZ_89' => ['flavorText' => ''],
      'ALT_DUSTEROP_P_YZ_91' => ['flavorText' => ''],
      'ALT_MUSUBI_B_AX_09' => ['flavorText' => ''],
      'ALT_MUSUBI_B_BR_04' => ['flavorText' => ''],
      'ALT_MUSUBI_B_LY_06' => ['flavorText' => ''],
      'ALT_MUSUBI_B_MU_22' => ['flavorText' => ''],
      'ALT_MUSUBI_B_OR_09' => ['flavorText' => ''],
      'ALT_MUSUBI_B_YZ_09' => ['flavorText' => ''],
      'ALT_WCQ25_P_AX_08' => ['flavorText' => ''],
      'ALT_WCQ25_P_BR_06' => ['flavorText' => ''],
      'ALT_WCQ25_P_LY_10' => ['flavorText' => ''],
      'ALT_WCQ25_P_MU_16' => ['flavorText' => ''],
      'ALT_WCQ25_P_OR_05' => ['flavorText' => ''],
      'ALT_WCQ25_P_YZ_05' => ['flavorText' => ''],
      'ALT_WCS26_P_AX_95_E' => ['flavorText' => ''],
      'ALT_WCS26_P_BR_98_E' => ['flavorText' => ''],
      'ALT_WCS26_P_LY_98_E' => ['flavorText' => ''],
      'ALT_WCS26_P_MU_96_E' => ['flavorText' => ''],
      'ALT_WCS26_P_OR_97_E' => ['flavorText' => ''],
      'ALT_WCS26_P_YZ_94_E' => ['flavorText' => ''],
    ];
  }

  /**
   * Go trough all played cards to apply effects
   */
  public static function getAllCardsWithMethod($methodName)
  {
    return self::getListeningCardsObject()->filter(function ($card) use ($methodName) {
      return \method_exists($card, 'on' . $methodName) ||
        \method_exists($card, 'onPlayer' . $methodName) ||
        \method_exists($card, 'onOpponent' . $methodName);
    });
  }

  public static function applyEffects($player, $methodName, &$args)
  {
    // Compute a specific ordering if needed
    $cards = self::getAllCardsWithMethod($methodName)->toAssoc();
    $nodes = array_keys($cards);
    $edges = [];
    $orderName = 'order' . $methodName;
    foreach ($cards as $cId => $card) {
      if (\method_exists($card, $orderName)) {
        foreach ($card->$orderName() as $constraint) {
          $cId2 = $constraint[1];
          if (!in_array($cId2, $nodes)) {
            continue;
          }
          $op = $constraint[0];

          // Add the edge
          $edge = [$op == '<' ? $cId : $cId2, $op == '<' ? $cId2 : $cId];
          if (!in_array($edge, $edges)) {
            $edges[] = $edge;
          }
        }
      }
    }
    $topoOrder = Utils::topological_sort($nodes, $edges);
    $orderedCards = [];
    foreach ($topoOrder as $cId) {
      $orderedCards[] = $cards[$cId];
    }

    // Apply effects
    $result = false;
    foreach ($orderedCards as $card) {
      $res = self::applyEffect($card, $player, $methodName, $args, false);
      $result = $result || $res;
    }
    return $result;
  }

  public static function applyEffect($card, $player, $methodName, &$args, $throwErrorIfNone = false)
  {
    $card = $card instanceof \ALT\Models\Card ? $card : self::get($card);
    $res = null;
    $listened = true;
    $isPlayerEvent = $player->getId() == $card->getPId();
    $node = ['type' => NODE_SEQ, 'optional' => true, 'childs' => []];

    list($payment, $output) = $card->getReactions($args);
    // throw new \feException(print_r($output));
    if (is_null($output) || empty($output)) {
      $listened = false;
      return null;
    }

    // if ($player != null && $isPlayerEvent && \method_exists($card, 'onPlayer' . $methodName)) {
    //   $n = 'onPlayer' . $methodName;
    //   $res = $card->$n($player, $args);
    // } elseif ($player != null && !$isPlayerEvent && \method_exists($card, 'onOpponent' . $methodName)) {
    //   $n = 'onOpponent' . $methodName;
    //   $res = $card->$n($player, $args);
    // } elseif (\method_exists($card, 'on' . $methodName)) {
    //   $n = 'on' . $methodName;
    //   $res = $card->$n($player, $args);
    // } else {
    //   $listened = false;
    // }

    if ($throwErrorIfNone && !$listened) {
      // throw new \feException(print_r(debug_print_backtrace()));
      throw new \BgaVisibleSystemException(
        'Trying to apply effect of a card without corresponding listener : ' . $methodName . ' ' . $card->getId()
      );
    }

    if (!is_null($payment) && !empty($payment)) {
      Utils::tagTree($payment, ['sourceId' => $card->getId()]);
      $node['childs'][] = $payment;
    }

    if (!is_null($output) && !empty($output)) {
      $output = Utils::tagTree($output, ['sourceId' => $card->getId()]);
      $output = Utils::bindOwnEffectActivateCardId($output, $card->getId());
      // if (isset($args['pId'])) {
      //   $output = Utils::tagTree($output, ['pId' => $args['pId']]);
      //   // throw new \feException(print_r($output));
      // }
      if (is_null($payment) || empty($payment)) {
        return $output;
      }
      $node['childs'][] = $output;
    }

    return $node;
  }
}
