<?php

namespace ALT\Actions;

use ALT\Managers\Cards;
use ALT\Core\Notifications;

class Exhaust extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_EXHAUST;
  }

  public function getDescription()
  {
    return '{T}';
  }

  public function isDoable($player)
  {
    return $this->getCard()->isTapped() == false;
  }

  public function getCard()
  {
    $cardId = $this->getCtxArg('cardId');
    if ($cardId == ME) {
      $cardId = $this->ctx->getSourceId() ?? null;
    } elseif ($cardId == EFFECT) {
      $cardId = $this->getCtx()->toArray()['event']['cardId'] ?? null;
    }

    if (is_null($cardId)) {
      throw new \Bga\GameFramework\VisibleSystemException('no card in args (Gain). Should not happen');
    }
    return Cards::getSingle($cardId);
  }

  public function stExhaust()
  {
    $player = $this->getPlayer();
    $card = $this->getCard();
    if (is_null($card)) {
      $this->resolveAction();
      return;
    }

    // Heroes stay on their own board slot, they are exhaustable there
    $location = $card->getLocation();
    $onHeroBoard = $card->getType() == HERO && str_starts_with($location, 'board-hero-');

    // if the card was a token or has been discarded / put to hand
    if (!$onHeroBoard && !in_array($location, [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE])) {
      $this->resolveAction();
      return;
    }

    if ($card->isTapped()) {
      throw new \BgaVisibleSystemException('Card is already tapped. Should not happen');
    }
    $card->setTapped(true);

    Notifications::exhaustEffect($player, $card, $this->getSource());
    // Check listener
    $this->checkAfterListeners($player, [
      'cardId' => $card->getId(),
      'cardLocation' => $card->getLocation(),
      'sourceId' => $this->getSourceId(),
      'token' => $card->isToken(),
    ]);

    $this->resolveAction();
  }
}
