<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Engine;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;
use ALT\Helpers\Conditions;

class Gain extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_GAIN;
  }

  public function getDescription()
  {
    $player = $this->getPlayer();
    $gain = $this->getGain();
    $desc = Utils::resourcesToStr([$gain[0] => $gain[1]], true);
    $upTo = $this->getUpTo();

    if ($this->getArg('augment') == true) {
      return [
        'log' => clienttranslate('augment'),
        'args' => [],
      ];
    }

    if ($upTo >= 99) {
      if ($player->getId() == Players::getActiveId()) {
        return [
          'log' => clienttranslate('Gain ${resources_desc}'),
          'args' => [
            'resources_desc' => $desc,
          ],
        ];
      }
      // The reward is for someone else
      else {
        return [
          'log' => clienttranslate('Let ${player_name} gain ${resources_desc}'),
          'args' => [
            'player_name' => $player->getName(),
            'resources_desc' => $desc,
          ],
        ];
      }
    } else {
      if ($player->getId() == Players::getActiveId()) {
        return [
          'log' => clienttranslate('Gain ${resources_desc}  (up to ${upTo})'),
          'args' => [
            'resources_desc' => $desc,
            'upTo' => $upTo,
          ],
        ];
      }
      // The reward is for someone else
      else {
        return [
          'log' => clienttranslate('Let ${player_name} gain ${resources_desc} (up to ${upTo})'),
          'args' => [
            'player_name' => $player->getName(),
            'resources_desc' => $desc,
            'upTo' => $upTo
          ],
        ];
      }
    }
  }

  public function isAutomatic($player = null)
  {
    // $cards = Cards::getPlayedCards(null);
    // $gain = $this->getArg('type');
    // foreach ($cards as $cId => $card) {
    //   $block = $card->getBlockAutomaticAction();
    //   if (isset($block[GAIN]) && isset($block[GAIN][$gain])) {
    //     return false;
    //   }
    // }
    return true;
  }

  public function isDoable($player)
  {
    if ($this->getCtxArg('cardId') == ME) {
      $event = $this->getEventRecursive();
      if (!is_null($event) && isset($event['action']) && $event['action'] == 'Discard' && $event['sourceLocation'] == DISCARD_PILE) {
        return false;
      }
      if (!is_null($event) && isset($event['action']) && $event['action'] == 'ChooseAssignment' && $event['sourceLocation'] == RESERVE) {
        $card = Cards::get($this->ctx->getSourceId());
        if (in_array($card->getId(), $event['reserveToListen'] ?? []) && $card->getLocation() != RESERVE) {
          return false;
        }
      }
      $card = Cards::get($this->ctx->getSourceId());
      list($gain, $n) = $this->getGain();
      if ($card->getType() == CHARACTER && $gain == FLEETING && $card->hasToken(FLEETING)) {
        return false;
      }
      // if (in_array(($event['sourceLocation']) ?? 'all'), []
      return true;
    } else {
      return true;
    }
  }

  // public function isOptional($player = null)
  // {
  //   if ($this->getCtxArg('cardId') == ME) {
  //     $event = $this->getEventRecursive();
  //     if (!is_null($event) && isset($event['action']) && $event['action'] == 'Discard' && $event['sourceLocation'] == DISCARD_PILE) {
  //       return true;
  //     }
  //     if (!is_null($event) && isset($event['action']) && $event['action'] == 'ChooseAssignment' && $event['sourceLocation'] == RESERVE) {
  //       $card = Cards::get($this->ctx->getSourceId());
  //       if (in_array($card->getId(), $event['reserveToListen'] ?? []) && $card->getLocation() != RESERVE) {
  //         return true;
  //       }
  //     }
  //     $card = Cards::get($this->ctx->getSourceId());
  //     list($gain, $n) = $this->getGain();
  //     if ($card->getType() == CHARACTER && $gain == FLEETING && $card->hasToken(FLEETING)) {
  //       return true;
  //     }
  //   }
  //   return parent::isOptional($player);
  // }

  public function isIndependent($player = null)
  {
    // return false;
    $cards = Cards::getPlayedCards(null);
    if ($this->getArg('augment')) {
      $gain = BOOST;
    } else {
      $gain = $this->getArg('type');
    }
    foreach ($cards as $cId => $card) {
      $block = $card->getBlockAutomaticAction();
      if (isset($block[GAIN]) && isset($block[GAIN][$gain])) {
        return false;
      }
    }
    return true;
  }

  public function getPlayer()
  {
    $pId = $this->getCtxArg('pId') ?? Players::getActiveId();
    return Players::get($pId);
  }

  public function getCard()
  {
    $cardId = $this->getCtxArg('cardId');
    if ($cardId == ME) {
      $cardId = $this->ctx->getSourceId() ?? null;
    } elseif ($cardId == EFFECT) {
      $event = $this->getEventRecursive();
      $cardId = null;
      if (!is_null($event)) {
        $cardId = $event['cardId'] ?? null;
        if (is_null($cardId)) {
          $cardId = $event['gain']['cardId'] ?? null;
        }
      }
    }

    if (is_null($cardId)) {
      throw new \BgaVisibleSystemException('no card in args (Gain). Should not happen');
    }
    return Cards::getSingle($cardId);
  }

  protected $args = [
    'n' => 1,
    'augment' => false,
    'type' => '',
    'upTo' => 99
  ];

  public function getGain()
  {
    if ($this->getArg('augment') === true) {
      return ['augment', 1];
    }
    $n = $this->getArg('n');
    if ($n == 'sourceCounter2') {
      $source = $this->getSource();
      if (!is_null($source)) {
        $n = ($source->getExtraDatas()['counter'] ?? 0) + 2;
      }
    }
    return [$this->getArg('type'), $n];
  }

  public function getUpTo()
  {
    $upTo = $this->getArg('upTo');
    if ($upTo >= 99) {
      return $upTo;
    }
    if ($this->getCtxArg('cardId') != EFFECT && !is_null($this->getCtxArg('cardId')) && $this->getCard()->getLocation() == RESERVE) {
      if ($this->getCtxArg('cardId') == ME && is_null($this->ctx->getSourceId())) {
        return $upTo;
      }
      return $this->getCard()->getPlayer()->getReserveAdd() + $upTo;
    } else {
      return $upTo;
    }
  }

  /**
   * Identifier of the distribution this gain belongs to, null if the gain stands alone.
   *
   * Effects distributing several counters at once (eg. "Distribute 4 boosts...", see
   * FT::SEQ_DISTRIBUTE_GAINS) are a single gain event: abilities listening on a gain
   * ("gains 1 or more boosts") must only trigger once for the whole distribution.
   */
  protected function getGainGroup()
  {
    $node = $this->ctx;
    while (is_object($node)) {
      if ($node->getInfos()['groupGains'] ?? false) {
        if (is_null($node->getInfos()['gainGroupId'] ?? null)) {
          $node->setInfo('gainGroupId', 'gains-' . substr(md5(uniqid('', true)), 0, 12));
          Engine::save();
        }
        return $node->getInfos()['gainGroupId'];
      }
      $node = $node->getParent();
    }

    return null;
  }

  public function gain($player, $card, $resource, $amount = 1, $source = null, $args = [])
  {
    $dynamicReplace = $card->getDynamicGainReplace();
    $args['cardId'] = $card->getId();

    if (in_array($card->getLocation(), [HAND, DISCARD_PILE])) {
      return;
    }

    if (is_null($source)) {
      $sourceId = -1;
    } else {
      $sourceId = $source->getId();
    }

    // Some effects change what is gained
    if (isset($dynamicReplace[$resource])) {
      $oldResource = $resource;
      $resource = $dynamicReplace[$resource];
      Notifications::message(
        clienttranslate('${old_resource} is replaced by ${resource} (${card_name}\'s effect)'),
        [
          'resource' => $resource,
          'old_resource' => $oldResource,
          'card' => $card,
          'i18n' => ['resource', 'old_resource'],
        ]
      );
      $args['type'] = $resource;
    }
    $args['type'] = $resource;

    if (in_array($resource, [FLEETING, ASLEEP, ANCHORED]) && $card->hasToken($resource)) {
      if ($card->isCanAlwaysGainFleeting()) {
        $this->checkAfterListeners($player, ['gain' => $args, 'sourceId' => $sourceId, 'token' => $card->isToken(),]);
      }
      // a card cannot have more than one fleeting/anchored token
      return;
    }
    if ($amount <= 0) {
      return;
    }

    $initialBoost = 0;
    if ($resource == BOOST) {
      $initialBoost = $card->countToken(BOOST);
    }

    $tokens = Meeples::createOnCard($resource, $card->getId(), $player->getId(), $amount);
    Notifications::gainMeeple($resource, $card, $tokens, $source, false);

    $listeners = ['gain' => $args, 'cardId' => $card->getId(), 'location' => $card->getLocation(), 'initialBoost' => $initialBoost, 'cardType' => $card->getType(), 'additionalType' => $card->getAdditionalType(), 'sourceId' =>  $sourceId, 'token' => $card->isToken(),];
    $gainGroup = $this->getGainGroup();
    if (!is_null($gainGroup)) {
      $listeners['gainGroup'] = $gainGroup;
    }
    $this->checkAfterListeners($player, $listeners);
  }

  protected function isCantGainBoostRuleActive($sourceCard, $rule)
  {
    if (!is_string($rule) || !str_starts_with($rule, 'character:excludeSelf')) {
      return false;
    }

    $conditionsStr = substr($rule, strlen('character:excludeSelf'));
    if ($conditionsStr === '' || $conditionsStr === false) {
      return true;
    }
    if ($conditionsStr[0] === ':') {
      $conditionsStr = substr($conditionsStr, 1);
    }
    if ($conditionsStr === '') {
      return true;
    }

    $conditions = str_contains($conditionsStr, '|')
      ? explode('|', $conditionsStr)
      : [$conditionsStr];

    return Conditions::check(['conditions' => $conditions], $sourceCard, []);
  }

  protected function isBoostGainBlockedByPassive($card, $resource)
  {
    if ($resource != BOOST || !in_array($card->getType(), [CHARACTER, TOKEN])) {
      return false;
    }

    foreach ($card->getPlayer()->getPlayedCards() as $sourceCard) {
      if ($sourceCard->getId() == $card->getId()) {
        continue;
      }

      $cantGainBoost = $sourceCard->getProperty('cantGainBoost');
      if (is_null($cantGainBoost)) {
        continue;
      }

      $rules = is_array($cantGainBoost) ? $cantGainBoost : [$cantGainBoost];
      foreach ($rules as $rule) {
        if ($this->isCantGainBoostRuleActive($sourceCard, $rule)) {
          return true;
        }
      }
    }

    return false;
  }

  public function stGain()
  {
    $player = $this->getPlayer();
    $source = $this->ctx->getSource() ?? null;
    $sourceId = $this->ctx->getSourceId() ?? null;
    if (is_null($source) && !is_null($sourceId)) {
      $source = Cards::getSingle($sourceId);
    }
    $card = $this->getCard();
    $args = $this->getCtxArgs();
    $upTo = $this->getUpTo();

    list($resource, $amount) = $this->getGain();

    if ($card->getLocation() == RESERVE && Players::hasBlockOpponentReserveGain($player)) {
      Notifications::message(clienttranslate('No counter can be gained in Reserve'), []);
      $this->resolveAction([]);
      return;
    }


    if ($resource == 'augment') {
      if (in_array($card->getLocation(), [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE]) && Players::hasBlockGainNewCounters()) {
        Notifications::message(clienttranslate('No new counter can be added to cards'), []);
        $this->resolveAction([]);
        return;
      }

      if ($card->countToken(BOOST) > 0) {
        $resource = BOOST;
      } else {
        // we need to increase the counter
        $data = $card->getExtraDatas();
        $data['counter'] = ($data['counter'] ?? 0) + 1;
        $card->setExtraDatas($data);

        Notifications::gainCounter($card, 1);
        $this->checkAfterListeners($card->getPlayer(), ['specialEffect' => 'gainCounter', 'augment' => true, 'cardId' => $card->getId(), 'token' => $card->isToken(),], true, 'SpecialEffect');
        $this->resolveAction([]);
        return;
      }
    }

    if ($resource == BOOST && in_array($card->getLocation(), [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE]) && $card->hasCounters() && Players::hasBlockGainNewCounters()) {
      Notifications::message(clienttranslate('No new boost can be added to cards'), []);
      $this->resolveAction([]);
      return;
    }

    if ($this->isBoostGainBlockedByPassive($card, $resource)) {
      Notifications::message(clienttranslate('This Character can\'t gain boosts'), ['card' => $card]);
      $this->resolveAction([]);
      return;
    }

    // check that we are not going to gain more than necessary
    $owned = $card->countToken($resource);
    if ($owned >= $upTo) {
      $this->resolveAction([]);
      return;
    } elseif (($owned + $amount) > $upTo) {
      $amount = $upTo - $owned;
    }


    $this->gain($player, $card, $resource, $amount, $source, $args);
    $this->resolveAction();
  }
}
