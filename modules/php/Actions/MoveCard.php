<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Globals;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;
use ALT\Core\Engine;
use ALT\Helpers\FT;

class MoveCard extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_MOVE_CARD;
  }

  public function getDescription()
  {
    if ($this->getArg('cards') == ALL) {
      return clienttranslate('move all characters to opposite expedition');
    }
    if (($this->getCtxArg('cardId') ?? null) == null || $this->getCtxArg('cardId') == EFFECT) {
      return clienttranslate('move a character to opposite expedition');
    }

    return [
      'log' => clienttranslate('move ${card_name} to opposite expedition'),
      'args' => ['card_name' => $this->getCard()->getName(), 'i18n' => ['card_name']],
    ];
    return clienttranslate('Move expedition');
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  protected $args = ['location' => 'opposite', 'player' => ME, 'cards' => 1, 'skipProtectAnchored' => false, 'skipProtectAsleep' => false];

  public function getCard()
  {
    $args = $this->getCtxArgs();
    $cardId = $args['cardId'] ?? null;
    if ($cardId === null) {
      throw new \Bga\GameFramework\VisibleSystemException('no card in args. Should not happen');
    } elseif ($cardId == EFFECT) {
      $cardId = $this->getCtx()->toArray()['event']['cardId'] ?? null;
      if (is_null($cardId)) {
        $cardId = $this->getCtx()->toArray()['event']['gain']['cardId'] ?? null;
      }
    } elseif ($cardId == ME) {
      $cardId = $this->getSourceId();
    }
    return Cards::get($cardId);
  }

  public function stMoveCard()
  {
    if ($this->getArg('cards') == ALL) {
      // we must switch all
      $player = $this->getArg('player') == ME ? Players::getActive() : Players::getNext(Players::getActive());
      $cards = $player->getPlayedCards(CHARACTER)->merge($player->getPlayedCards(TOKEN));
    } else {
      $cards[] = $this->getCard();
    }
    $source = $this->getSource();

    $map = [STORM_LEFT => STORM_RIGHT, STORM_RIGHT => STORM_LEFT];
    foreach ($cards as $cId => $card) {
      $fromLocation = $card->getLocation();

      // Floral Tent
      if (
        !$this->getArg('skipProtectAnchored') &&
        Globals::isDayPhase() &&
        in_array($fromLocation, STORMS) &&
        in_array($card->getType(), [TOKEN, CHARACTER]) &&
        $card->getPlayer()->hasProtectAnchoredInExpedition($fromLocation) &&
        $card->hasToken(ANCHORED)
      ) {
        unset($cards[$cId]);
        Notifications::message(clienttranslate('${card_name} is not discarded but loose <ANCHORED> instead'), ['card' => $card]);
        $this->insertAsChild(['action' => LOOSE, 'args' => ['cardId' => $card->getId(), 'type' => ANCHORED]]);
        continue;
      }

      // Fane of Calypso (Anchored)
      if (
        !$this->getArg('skipProtectAnchored') &&
        Globals::isDayPhase() &&
        in_array($fromLocation, STORMS) &&
        in_array($card->getType(), [TOKEN, CHARACTER]) &&
        $card->hasToken(ANCHORED)
      ) {
        $player = $card->getPlayer();
        if (!$player->hasProtectAnchoredInExpedition($fromLocation, $card->isGigantic())) {
          $altNode = FT::ACTION(MOVE_CARD, [
            'cardId' => $cId,
            'location' => $this->getArg('location'),
            'player' => $this->getArg('player'),
            'skipProtectAnchored' => true,
            'skipProtectAsleep' => true,
          ], ['pId' => $card->getPId()]);
          $toAdd = $player->buildSacrificeProtectAnchoredChoice($cId, $altNode);
          if ($toAdd) {
            unset($cards[$cId]);
            $toAdd['sourceId'] = $cId;
            $this->insertAsChild($toAdd);
            continue;
          }
        }
      }

      // Fane of Calypso (Ordis)
      if (
        !$this->getArg('skipProtectAsleep') &&
        Globals::isDayPhase() &&
        in_array($fromLocation, STORMS) &&
        in_array($card->getType(), [TOKEN, CHARACTER]) &&
        $card->hasToken(ASLEEP)
      ) {
        $player = $card->getPlayer();
        $altNode = FT::ACTION(MOVE_CARD, [
          'cardId' => $cId,
          'location' => $this->getArg('location'),
          'player' => $this->getArg('player'),
          'skipProtectAnchored' => true,
          'skipProtectAsleep' => true,
        ], ['pId' => $card->getPId()]);
        $toAdd = $player->buildSacrificeProtectAsleepChoice($cId, $altNode);
        if ($toAdd) {
          unset($cards[$cId]);
          $toAdd['sourceId'] = $cId;
          $this->insertAsChild($toAdd);
          continue;
        }
      }

      // Floral tent bravos
      if (
        !$this->getArg('skipProtectAnchored') &&
        Globals::isDayPhase() &&
        in_array($fromLocation, STORMS) &&
        in_array($card->getType(), [TOKEN, CHARACTER]) &&
        $card->getPlayer()->hasProtectBoostedInExpedition($fromLocation) &&
        $card->hasToken(BOOST)
      ) {
        unset($cards[$cId]);
        Notifications::message(clienttranslate('${card_name} is not discarded but loose <BOOST> instead'), ['card' => $card]);
        $this->insertAsChild(['action' => LOOSE, 'args' => ['cardId' => $card->getId(), 'type' => BOOST, 'n' => 99]]);
        continue;
      }

      if ($this->getArg('location') == 'opposite') {
        $card->setLocation($map[$card->getLocation()]);
      }

      Notifications::moveCard($source->getPlayer(), $card, $source);

      $expeditionsBoosts = Globals::getNextCharacterInExpeditionBoost();
      if ($card->getType() == CHARACTER && isset($expeditionsBoosts[$card->getPId()][$map[$card->getLocation()]]) && !$card->isGigantic()) {
        $this->insertAsChild(['action' => BOOST, 'args' => ['cardId' => $card->getId(), 'type' => BOOST, 'n' => $expeditionsBoosts[$card->getPId()][$map[$card->getLocation()]]]]);
        unset($expeditionsBoosts[$player->getId()][$map[$card->getLocation()]]);
        Globals::setNextCharacterInExpeditionBoost($expeditionsBoosts);
      }

      $this->checkAfterListeners($source->getPlayer(), [
        'cardId' => $card->getId(),
        'playCard' => true,
        'cardType' => $card->getType(),
        'additionalType' => $card->getAdditionalType(),
        'cardSubtypes' => $card->getSubtypes(),
        'from' => $fromLocation,
        'to' => $card->getLocation(),
        'locationPId' => $card->getPId(),
        'token' => $card->isToken()
      ]);
    }

    $this->resolveAction(null);
  }
}
