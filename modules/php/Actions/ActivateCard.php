<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Core\Engine;
use ALT\Core\Globals;
use ALT\Helpers\Utils;

class ActivateCard extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_ACTIVATE_CARD;
  }

  public function getCard()
  {
    $card = Cards::getSingle($this->getCtxArg('cardId'), false);

    // Tokens are not really destroyed, they got to location "destroy"
    //  but they cant be activated from here => only case is Maw
    if (!is_null($card) && ($card->getLocation() == 'destroy' && !in_array($this->getCtxArg('cardId'), $this->getCtxArgs()['event']['cardsToListen'] ?? []))) {
      return null;
    }
    return $card;
  }

  public function getFlow($player)
  {
    $card = $this->getCard();
    if (is_null($card)) {
      // in the case of a token that react, as we deleted the tokens they do not exist in DB anymore
      return null;
    }

    $event = $this->getCtxArg('event');
    // if (
    //   $card->isPlayed() || $card->getLocation() == RESERVE || in_array($this->getCtxArg('cardId'), $this->getCtxArgs()['event']['cardsToListen'] ?? [])
    // ) {
    //   throw new \feException($card->getId());
    // }
    // throw new \feException($this->getCtxArg('cardId'));
    $flow =
      $card->isPlayed() ||
      $card->getLocation() == RESERVE ||
      in_array($this->getCtxArg('cardId'), $this->getCtxArgs()['event']['cardsToListen'] ?? []) ||
      in_array($this->getCtxArg('cardId'), $this->getCtxArgs()['event']['reserveToListen'] ?? []) // cas of infinity effect to listen after discard
      ?
      Cards::applyEffect(
        $card,
        $player,
        $event['method'],
        $event,
        false // Throw error if no such listener
      )
      : null;

    if ($flow == null) {
      return null;
    }

    if (($flow['pId'] ?? -1) == CONTROLLER) {
      $tagPId = $event['controller'] ?? $event['pId'];
    } elseif (isset($event['owner'])) {
      $tagPId = $event['owner'] ?? $event['pId'];
    } elseif (isset($event['cardPId']) && ($event['cardId'] ?? null) === $this->getCtxArg('cardId')) {
      // The reacting card is the one the event is about, and the event snapshotted the
      // controller it had while it was still in its zone. Card::discardTo() restores player_id
      // to the owner as soon as the card leaves, so Cards::get()->getPId() would wrongly give the
      // owner back (e.g. Aerolith taken with Milady de Winter: its controller must Resupply,
      // not the player it was stolen from).
      $tagPId = $event['cardPId'];
    } else {
      $tagPId = Cards::get($this->getCtxArg('cardId'))->getPId();
    }

    $flow = Utils::tagTree($flow, [
      'sourceId' => $this->getCtxArg('cardId'),
      'event' => $event,
      'pId' => $tagPId,
    ]);

    // if we have a card invoking with the parameter source we substitute it with current location
    // Only if the card is in Storms
    if ((isset($event['from']) && in_array($event['from'], [STORM_LEFT, STORM_RIGHT]))) { // || in_array($card->getLocation(), [STORM_LEFT, STORM_RIGHT])) {
      $flow = Utils::updateTree($flow, [0 => 'source'], [$event['from'] ?? $card->getLocation()], ['targetLocation']);
    }
    // throw new \feException(print_r($flow));
    return $flow;
  }

  public function getFlowTree($player)
  {
    $flow = $this->getFlow($player);
    // throw new \feException(print_r(debug_print_backtrace()));

    return is_null($flow) ? null : Engine::buildTree($flow);
  }

  public function isOptional($player)
  {
    $player = $this->getPlayer();
    if (is_null($this->getFlowTree($player))) {
      return true;
    }
    return $this->getFlowTree($player)->isOptional($player) || !$this->getFlowTree($player)->isDoable($player) || Globals::getEngineAutomatic() > 20;
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  public function isDoable($player)
  {
    $player = $this->getPlayer();
    $flowTree = $this->getFlowTree($player);

    return is_null($flowTree) ? false : $flowTree->isDoable($player);
  }

  public function isIrreversible($player = null)
  {
    $player = $this->getPlayer();
    $flowTree = $this->getFlowTree($player);
    return is_null($flowTree) ? false : $flowTree->isIrreversible();
  }

  public function isIndependent($player = null)
  {
    $player = $this->getPlayer();
    $flowTree = $this->getFlowTree($player);
    return is_null($flowTree) ? false : $flowTree->isIndependent($player)  && Globals::getEngineAutomatic() < 20;
  }

  public function getDescription()
  {
    $flowTree = $this->getFlowTree($this->getPlayer());
    if (is_null($flowTree)) {
      return '';
    }
    // throw new \feException(print_r($flowTree->toArray()));

    $flowDesc = $flowTree->getDescription();
    return [
      'log' => '${flowDesc} (${source})',
      'args' => [
        'i18n' => ['flowDesc', 'source'],
        'flowDesc' => $flowDesc,
        'source' => $this->getCard()->getName(),
      ],
    ];
  }

  public function stActivateCard()
  {
    $player = $this->getPlayer();
    $node = $this->ctx;
    $flow = $this->getFlow($player);
    if ($node->isMandatory()) {
      $flow['optional'] = false; // Remove optional to avoid double confirmation UX
    }

    // throw new \feException(print_r($flow));
    // Test Defect
    // $flow['pId'] = $this->getCard()
    //   ->getPlayer()
    //   ->getId();

    // $flow['pId'] = $player->getId();

    // Add tag about that card
    // $flow = Utils::tagTree($flow, [
    //   'sourceId' => $this->getCtxArg('cardId'),
    //   'event' => $this->getCtxArg('event'),
    // ]);
    // throw new \feException(Globals::getEngineAutomatic());
    // if (Globals::getEngineChoices() > 20 || Globals::getEngineAutomatic() > 20) {
    //   throw new \feException("titi");
    // }
    $node->replace(Engine::buildTree($flow));
    Engine::save();
    Engine::proceed();
  }
}
