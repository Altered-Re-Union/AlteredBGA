<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;

class BoostExchange extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_BOOST_EXCHANGE;
  }

  public function getDescription()
  {
    return clienttranslate('Exchange boosts between 2 cards');
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  public function isIndependent($player = null)
  {
    return true;
  }

  public function getPlayer()
  {
    $pId = $this->getCtxArg('pId') ?? Players::getActiveId();
    return Players::get($pId);
  }

  protected $args = [
    'cardId' => null
  ];

  public function stBoostExchange()
  {
    $player = $this->getPlayer();
    $allIds = $this->resolveSwapIds($this->getArg('cardId'));
    if (is_null($allIds) || count($allIds) != 2) {
      throw new \BgaVisibleSystemException('No Ids to switch in boostExchange, should not happen');
    }

    $source = $this->ctx->getSource() ?? null;
    $sourceId = $this->ctx->getSourceId() ?? null;
    if (is_null($source) && !is_null($sourceId)) {
      $source = Cards::getSingle($sourceId);
    }
    $card1 = $allIds[0];
    $card2 = $allIds[1];


    $allCards = Cards::get($allIds);
    $existingBoost = [];
    $existingBoost[$card2] = $allCards[$card1]->countToken(BOOST);
    $existingBoost[$card1] = $allCards[$card2]->countToken(BOOST);

    $meeplesCard1 = $allCards[$card1]->getOfType(BOOST);
    $meeplesCard2 = $allCards[$card2]->getOfType(BOOST);

    if ($meeplesCard1->count() > 0) {
      Meeples::delete($meeplesCard1->getIds());
      Notifications::looseMeeples(BOOST, $allCards[$card1], $meeplesCard1, false);
    }
    if ($existingBoost[$card2] > 0) {
      $tokens = Meeples::createOnCard(BOOST, $allCards[$card2]->getId(), $allCards[$card2]->getPlayer()->getId(), $existingBoost[$card2]);
      Notifications::gainMeeple(BOOST, $allCards[$card2], $tokens, $source, false);
      if ($existingBoost[$card2] > $existingBoost[$card1]) {
        $this->checkAfterListeners($player, ['gain' => ['cardId' => $allCards[$card2]->getId(), 'type' => BOOST, 'n' => $existingBoost[$card2]], 'token' => $allCards[$card2]->isToken(), 'cardId' => $allCards[$card2]->getId(), 'location' => $allCards[$card2]->getLocation(), 'initialBoost' => $existingBoost[$card1], 'cardType' => $allCards[$card2]->getType(), 'additionalType' => $allCards[$card2]->getAdditionalType(), 'sourceId' =>  $sourceId], true, 'Gain');
      }
    }

    if ($meeplesCard2->count() > 0) {
      Meeples::delete($meeplesCard2->getIds());
      Notifications::looseMeeples(BOOST, $allCards[$card2], $meeplesCard2, false);
    }
    if ($existingBoost[$card1] > 0) {
      $tokens = Meeples::createOnCard(BOOST, $allCards[$card1]->getId(), $allCards[$card1]->getPlayer()->getId(), $existingBoost[$card1]);
      Notifications::gainMeeple(BOOST, $allCards[$card1], $tokens, $source, false);
      if ($existingBoost[$card1] > $existingBoost[$card2]) {
        $this->checkAfterListeners($player, ['gain' => ['cardId' => $allCards[$card1]->getId(), 'type' => BOOST, 'n' => $existingBoost[$card1]], 'token' => $allCards[$card1]->isToken(), 'cardId' => $allCards[$card1]->getId(), 'location' => $allCards[$card1]->getLocation(), 'initialBoost' => $existingBoost[$card2], 'cardType' => $allCards[$card1]->getType(), 'additionalType' => $allCards[$card1]->getAdditionalType(), 'sourceId' =>  $sourceId], true, 'Gain');
      }
    }


    $this->resolveAction();
  }
  
  /**
   * Sap Duende passes two ids at once. Unique 839 is a nested TARGET: the second pick is
   * `cardId`, the first pick (or the unique itself) is recovered from ancestor ctx / source.
   */
  private function resolveSwapIds($allIds)
  {
    if (is_array($allIds)) {
      $ids = array_values($allIds);
      if (count($ids) == 2) {
        return $ids;
      }
      if (count($ids) != 1) {
        return null;
      }
      $allIds = $ids[0];
    }

    if (!$this->isResolvedCardId($allIds)) {
      return null;
    }

    $pickedId = $allIds;
    $otherId = $this->findPreviousTargetCardId($pickedId);
    if ($otherId === null) {
      $otherId = $this->getSourceId();
    }
    if (!$this->isResolvedCardId($otherId) || $otherId == $pickedId) {
      return null;
    }

    return [$pickedId, $otherId];
  }

  private function findPreviousTargetCardId($excludeId)
  {
    $ctx = $this->getCtx();
    $parent = ($ctx !== null && method_exists($ctx, 'getParent')) ? $ctx->getParent() : null;
    while ($parent !== null) {
      $args = method_exists($parent, 'getArgs') ? ($parent->getArgs() ?? []) : [];
      $cid = $args['cardId'] ?? null;
      if ($this->isResolvedCardId($cid) && $cid != $excludeId) {
        return $cid;
      }
      $parent = method_exists($parent, 'getParent') ? $parent->getParent() : null;
    }
    return null;
  }

  private function isResolvedCardId($cid)
  {
    return $cid !== null && $cid !== EFFECT && $cid !== ME && !is_array($cid) && is_numeric($cid);
  }
}
