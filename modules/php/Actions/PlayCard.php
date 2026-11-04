<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Managers\Actions;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;

class PlayCard extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_PLAY_CARD;
  }

  public function getDescription()
  {
    $player = $this->getPlayer();
    $card = $this->getCard(true);
    $msg = clienttranslate('Play ${card_name}');

    if (is_null($card)) {
      return clienttranslate('Play a card');
    }
    if ($this->getArg('cost') > 0) {
      $msg = clienttranslate('Play ${card_name} for ${mana_cost}');
    }

    return [
      'log' => $msg,
      'args' => [
        'card_name' => $card->getName(),
        'mana_cost' => $this->getArg('cost'),
        'i18n' => ['card_name'],
      ],
    ];
  }

  public function getPlayer()
  {
    $pId = $this->getCtxArg('pId') ?? Players::getActiveId();
    return Players::get($pId);
  }

  public function getCard($description = false)
  {
    $cardId = $this->getCtxArg('cardId');
    if ($cardId == ME) {
      $cardId = $this->ctx->getSourceId() ?? null;
    }

    if (is_null($cardId) && $description) {
      return null;
    } elseif (is_null($cardId)) {
      throw new \BgaVisibleSystemException('no card in args (play card). Should not happen');
    }
    return Cards::getSingle($cardId);
  }

  protected $args = [
    'n' => 1,
    'free' => false,
    'effectHand' => true,
    'location' => '',
    'cost' => 0,
    'costReduction' => 0,
    'reallyPlayed' => true,
    'stealOwnership' => false,
    // null = infer in playCard (free + cost 0 => not a turn play); true for cost-reduction paths
    'countsAsTurnPlay' => null,
  ];

  public function isDoable($player)
  {
    $card = $this->getCard();
    return !$card->isExhaustedReservePlayBlocked($player)
      && !empty($card->getPlayableLocation($player, null, $this->getArg('free')))
      && $card->getMinManaOrbs() <= $player->getTotalMana()
      && !$card->isNameBlockedThisDay($player);
  }

  public function argsPlayCard()
  {
    $card = $this->getCard();
    $cId = $card->getId();
    $player = Players::getActive();
    if (!$this->getArg('free') && !$card->canBePlayed($player)) {
      throw new \BgaVisibleSystemException('Card cannot be played. Should not happen');
    }

    $locations[$cId] = $card->getPlayableLocation($player, null, $this->getArg('free'));
    if ($card->hasTemple() && $card->canBePlayed($player, false, false, true)) {
      $locations[$cId] = array_values(array_unique(array_merge($locations[$cId], $card->getTemplePlayableLocations($player))));
    }
    // $type = $card->getType();
    // $subTypes = $card->getSubtypes();
    // if ($type == PERMANENT && !in_array(LANDMARK, $subTypes)) {
    //   $locations[$cId] = [PERMANENT];
    // } elseif ($type == PERMANENT && in_array(LANDMARK, $subTypes)) {
    //   $locations[$cId] = [LANDMARK];
    // } elseif ($type == SPELL) {
    //   $locations[$cId] = [LIMBO];
    // } elseif ($type == CHARACTER) {
    //   $locations[$cId] = [STORM_LEFT, STORM_RIGHT];
    // }

    return [
      'card_name' => $this->getCard()->getName(),
      'i18n' => ['card_name'],
      'cardId' => $cId,
      '_private' => ['active' => ['play' => $locations]],
      'descSuffix' => $this->getArg('free') ? 'free' : '',
    ];
  }

  public function stPlayCard()
  {
    if ($this->getArg('location') != '') {
      return [$this->getCard()->getId(), $this->getArg('location')];
    }
  }

  public function actPlayCard($cardId, $location)
  {
    $args = $this->argsPlayCard()['_private']['active']['play'];
    $locations = $args[$cardId] ?? null;
    if (is_null($locations)) {
      throw new \BgaVisibleSystemException('This card cannot be played. Should not happen');
    }
    if (!in_array($location, $locations)) {
      throw new \BgaVisibleSystemException('Invalid location to play a card. Should not happen');
    }
    $card = Cards::get($cardId);
    $player = Players::getActive();
    $temple = false;
    if ($location == LANDMARK && $card->hasTemple() && !in_array(LANDMARK, $card->getPlayableLocation($player))) {
      $temple = true;
    }
    if ($this->getArg('stealOwnership') && $card->getPId() != Players::getActiveId()) {
      $extraDatas = $card->getExtraDatas();
      if (!isset($extraDatas['pId'])) {
        $extraDatas['pId'] = $card->getPId();
        $card->setExtraDatas($extraDatas);
      }
      $card->setPId(Players::getActiveId());
    }

    $context = &$this->getCtx();
    Actions::get(CHOOSE_ASSIGNMENT, $context)->playCard(
      $cardId,
      $location,
      $this->getArg('free'),
      $this->getArg('effectHand'),
      $this->getArg('cost'),
      $this->getArg('reallyPlayed'),
      false,
      $this->getArg('stealOwnership'),
      $this->getArg('countsAsTurnPlay'),
      $temple
    );
  }
}
