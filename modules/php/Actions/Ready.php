<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;
use ALT\Core\Globals;

class Ready extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_READY;
  }

  public function getDescription()
  {
    $cardId = $this->getCtxArg('cardId');
    if (is_array($cardId)) {
      if (!empty($cardId) && Cards::getSingle($cardId[0])->getLocation() == MANA) {
        return clienttranslate('Ready mana orbs');
      }
      return clienttranslate('Ready the cards');
    }
    if ($cardId == MANA) {
      return clienttranslate('Ready a mana orb');
    } else {
      return clienttranslate('Ready the card');
    }
  }

  public function isAutomatic($player = null)
  {
    return is_array($this->getCtxArg('cardId'));
  }

  public function isIndependent($player = null)
  {
    return $this->isAutomatic($player);
  }

  public function isDoable($player)
  {
    $cardIds = $this->getResolvedCardIds();
    if (is_array($this->getCtxArg('cardId'))) {
      if (empty($cardIds)) {
        return false;
      }
      foreach ($cardIds as $cardId) {
        if (!Cards::getSingle($cardId)->isTapped() && is_null($this->getCtxArg('optionalExhaust'))) {
          return false;
        }
      }
      return true;
    }

    return $this->getCard()->isTapped() == true;
  }

  private function getResolvedCardIds()
  {
    $cardId = $this->getCtxArg('cardId');
    if (is_array($cardId)) {
      return $cardId;
    }
    if ($cardId == ME) {
      $cardId = $this->ctx->getSourceId() ?? null;
    } elseif ($cardId == EFFECT) {
      $cardId = $this->getCtx()->toArray()['event']['cardId'] ?? null;
    } elseif ($cardId == MANA) {
      $mana = $this->getPlayer()->getManaCards(true)->first();
      return is_null($mana) ? [] : [$mana->getId()];
    }

    if (is_null($cardId)) {
      throw new \BgaVisibleSystemException('no card in args (Ready). Should not happen');
    }
    return [$cardId];
    }
  
    public function getCard()
    {
      $cardIds = $this->getResolvedCardIds();
      if (count($cardIds) === 1) {
        return Cards::getSingle($cardIds[0]);
      }
      return null;
    }

  public function stReady()
  {
    $player = $this->getPlayer();
    $cards = Cards::getMany($this->getResolvedCardIds());

    foreach ($cards as $card) {
      if (!$card->isTapped() && is_null($this->getCtxArg('optionalExhaust'))) {
        throw new \BgaVisibleSystemException('Card is not tapped. Should not happen');
      }
      $card->setTapped(false);
      Notifications::readyEffect($player, $card, $this->getSource());
    }
    $this->resolveAction(['automatic' => count($cards) > 1]);
  }
}
