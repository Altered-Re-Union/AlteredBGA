<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Managers\ActionCards;
use ALT\Core\Engine;
use ALT\Core\Globals;
use ALT\Core\Stats;
use ALT\Helpers\FT;
use ALT\Helpers\Utils;
use ALT\Models\Player;

class InvokeToken extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_INVOKE_TOKEN;
  }

  protected $args = [
    'allPlayers' => false,
    'moreThan1' => false,
    'forcedLocation' => null,
    'n' => 1
  ];

  public function getDescription()
  {
    $msg = clienttranslate('Invoke ${tokenName} in ${location}');
    return [
      'log' => $msg,
      'args' => [
        'tokenName' => $this->getToken()->getName(),
        'location' => implode(', ', $this->getDisplayLocation()),
        'i18n' => ['tokenName', 'location'],
      ],
    ];
  }

  protected function resolveTargetOwnerId()
  {
    $ownerId = $this->getCtxArg('ownerId');
    if (is_null($ownerId) && !is_array($this->ctx)) {
      $ownerId = $this->ctx->infos['ownerId'] ?? null;
    }
    if (!is_null($ownerId) && $ownerId != -1) {
      return $ownerId;
    }
    $event = $this->getEvent();
    if (!is_null($event) && isset($event['owner'])) {
      return $event['owner'];
    }
    $source = $this->getSource();
    if (!is_null($source)) {
      return $source->getOwner();
    }
    $cardId = $this->getCtxArg('cardId') ?? $this->getSourceId();
    if (!is_null($cardId)) {
      return Cards::get($cardId)->getOwner();
    }
    return $this->getPlayer()->getId();
  }

  public function getInvokePlayerId()
  {
    if (isset($this->getCtxArgs()['expedition'])) {
      return $this->getCtxArgs()['player'] ?? $this->getPlayer()->getId();
    }

    $player = $this->getPlayer();
    $invokePId = $player->getId();
    $targetPlayer = $this->getCtxArg('targetPlayer');
    if (is_null($targetPlayer)) {
      return $invokePId;
    }

    if ($targetPlayer == OPPONENT) {
      return Players::getNextId($player);
    }
    if ($targetPlayer == 'owner') {
      // LeaveExpedition (e.g. Trojan Horse) has no cardId/ownerId on the invoke node;
      // resolve via event owner / source owner instead of Cards::get(null).
      return $this->resolveTargetOwnerId();
    }

    return $invokePId;
  }

  public function argsInvokeToken()
  {
    $player = Players::getActive();

    $tokenType = $this->resolveTokenType();
    $targetLocations = $this->getCtxArg('targetLocation') ?? STORMS;
    if (isset($this->getCtxArgs()['expedition'])) {
      // we come from a target Expedition
      $targetLocations = [$this->getCtxArgs()['expedition']];
    }

    $invokePlayerId = $this->getInvokePlayerId();

    return [
      'token' => $tokenType,
      'tokenType' => $this->getToken()->getName(),
      'n' => $this->getN(),
      'canPass' => $this->getCtxArg('optional') ?? false,
      'locations' => $targetLocations,
      'allPlayers' => (is_null($this->getCtxArg('targetPlayer')) && count($targetLocations) > 1) || ($this->getCtxArg('allPlayers') ?? false),
      'invokePlayerId' => $invokePlayerId,
      'invokeOnOpponent' => $invokePlayerId != $player->getId(),
    ];
  }

  public function getN()
  {
    $n = $this->getArg('n');
    if (is_int($n)) {
      return $n;
    } elseif ($n == 'landmarks3') {
      return min($this->getPlayer()->getLandmarks()->count(), 3);
    } elseif ($n == 'paidMana') {
      return $n = $this->getCtx()->getParent()->toArray()['childs'][0]['actionResolutionArgs'][0];
    }
  }

  public function getToken()
  {
    $tokenType = $this->resolveTokenType();
    $infos = explode('_', $tokenType);
    $className = "\\ALT\\Cards\\$infos[0]\\$tokenType";
    return new $className(null);
  }

  public function resolveTokenType()
  {
    $tokenType = $this->getCtxArg('tokenType');
    if ($tokenType === HERO_SIGNATURE) {
      $tokenType = $this->getPlayer()->getHeroSignatureTokenType();
      if (is_null($tokenType)) {
        throw new \BgaVisibleSystemException('Hero has no Signature token. Should not happen');
      }
    }
    return $tokenType;
  }

  public function isAutomatic($player = null)
  {
    if (isset($this->ctx->getInfos()['automatic'])) {
      return $this->ctx->getInfos()['automatic'];
    }
    return count($this->argsInvokeToken()['locations']) == 1;
  }

  public function isIndependent($player = null)
  {
    if (isset($this->ctx->getInfos()['independent'])) {
      return $this->ctx->getInfos()['independent'];
    }
    return count($this->argsInvokeToken()['locations']) == 1;
  }

  public function getLocationInfos($location)
  {
    $realLocation = $location;
    $strLocation = $location;
    if (in_array($location, ['source', 'oppositeSource', 'initialSource'])) {
      $source = $this->getSource();

      // No actual source => is that even possible ??
      if (is_null($source)) {
        $strLocation = $location != 'oppositeSource' ? clienttranslate('Source') : clienttranslate('Opposite source');
      }
      // Get the source location
      else {
        $srcLoc = $this->getSource()->getLocation();

        // Check event in case of leaving expedition
        $event = $this->getEventRecursive();
        if (!is_null($event) && in_array($event['method'], ['LeaveExpedition', 'LeaveLandmark']) && isset($event['from'])) {
          $srcLoc = $event['from'];
        }

        // Switch if needed
        if ($location == 'oppositeSource') {
          $realLocation = $srcLoc == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
        } else {
          $realLocation = $srcLoc;
        }

        $strLocation = $realLocation == STORM_LEFT ? clienttranslate('Hero\'s expedition') : clienttranslate('Companion\'s expedition');
      }
    }

    if ($location == 'eventTo') {
      $event = $this->getEventRecursive();
      $realLocation = null;
      if (!is_null($event) && isset($event['to']) && in_array($event['to'], STORMS)) {
        $realLocation = $event['to'];
      } elseif (!is_null($event) && isset($event['cardId'])) {
        $playedCard = Cards::get($event['cardId']);
        if (in_array($playedCard->getLocation(), STORMS)) {
          $realLocation = $playedCard->getLocation();
        }
      }
      if (is_null($realLocation)) {
        $realLocation = $this->getSource()->getLocation();
      }
      $strLocation = $realLocation == STORM_LEFT ? clienttranslate('Hero\'s expedition') : clienttranslate('Companion\'s expedition');
      return [$realLocation, $strLocation];
    }

    if ($location == 'discardedSource') {
      return [STORM_LEFT, 'discarded card source'];
    }
    if ($location == STORM_LEFT) {
      return [STORM_LEFT, clienttranslate('Hero expedition')];
    }
    if ($location == STORM_RIGHT) {
      return [STORM_RIGHT, clienttranslate('Companion expedition')];
    }


    return [$realLocation, $strLocation];
  }

  public function getDisplayLocation()
  {
    $locations = $this->getCtxArg('targetLocation') ?? STORMS;
    $displayLocation = [];
    foreach ($locations as $loc) {
      list($realLocation, $strLocation) = $this->getLocationInfos($loc);
      $displayLocation[] = $strLocation;
    }

    return $displayLocation;
  }

  public function stInvokeToken()
  {
    $args = $this->argsInvokeToken();

    if (($this->getCtxArg('targetLocation') ?? []) == ['oppositeSource'] && !is_null($this->getSourceId()) && Cards::get($this->getSourceId())->isGigantic()) {
      // Source is gigantic so no opposite expedition
      $this->resolveAction([PASS]);
      return;
    }
    if (count($args['locations']) == 1 && $this->getArg('allPlayers') == false) {
      $this->actInvokeToken($args['locations'][0], true);
    }
  }

  public function getPlayer()
  {
    $args = $this->getCtxArgs();
    $pId = $args['pId'] ?? Players::getActiveId();
    if ($pId == 'source' || $pId == OWNER) {
      $pId = $this->getSource()->getPId();
    } elseif ($pId == CONTROLLER) {
      $pId = $this->getEvent()['controller'] ?? $this->getSource()->getPId();
    }
    return Players::get($pId);
  }

  public function actInvokeToken($location, $auto = false)
  {
    // throw new \feException(print_r(debug_print_backtrace()));
    $player = $this->getPlayer();
    $args = $this->argsInvokeToken();

    // throw new \feException($location);

    $explodedLocation = explode('-', $location);
    if (isset($this->getCtxArgs()['expedition'])) {
      $invokePId = $this->getCtxArgs()['player'];
    } elseif (count($explodedLocation) == 1) {
      $invokePId = $player->getId();
    } else {
      $invokePId = $explodedLocation[1];
    }

    if (!is_null($this->getCtxArg('targetPlayer'))) {
      $targetPlayer = $this->getCtxArg('targetPlayer');
      if ($targetPlayer == OPPONENT) {
        $invokePId = Players::getNextId($player);
      } elseif ($targetPlayer == 'owner') {
        $invokePId = $this->resolveTargetOwnerId();
      }
    }

    if (!in_array($explodedLocation[0], $args['locations']) && count($explodedLocation) == 1) {
      throw new \Bga\GameFramework\VisibleSystemException('You cannot invoke in this location. Should not happen');
    }

    list($realLocation, $strLocation) = $this->getLocationInfos($explodedLocation[0]);
    $location = $realLocation;

    if (!is_null($this->getArg('forcedLocation'))) {
      $location = $this->getArg('forcedLocation');
    }

    for ($i = 0; $i < $this->getN(); $i++) {
      $card = $this->getToken();
      $card = Cards::singleCreate([
        'player_id' => $invokePId,
        'location' => $location,
        'nbr' => 1,
        'properties' => $card->getProperties(),
      ]);

      Notifications::invokeToken($player, $card, $this->getSource());

      // should we boost the card
      // 20250224 - Disabling with discussion with Maverick
      // if (Globals::getNextCharacterBoost() > 0) {
      //   $this->insertAsChild(FT::GAIN($card, BOOST, Globals::getNextCharacterBoost()));
      //   Globals::setNextCharacterBoost(0);
      // }

      if (Globals::getNextTokenAnchored() == true) {
        $this->insertAsChild(FT::GAIN($card, ANCHORED));
        Globals::setNextTokenAnchored(false);
      }
      if (Globals::getNextTokenAsleep() == true) {
        $this->insertAsChild(FT::GAIN($card, ASLEEP));
        Globals::setNextTokenAsleep(false);
      }

      $expeditionsBoosts = Globals::getNextCharacterInExpeditionBoost();
      $boostExpedition = $location;
      $oppositeExpedition = $location == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
      if (
        !isset($expeditionsBoosts[$player->getId()][$location]) &&
        $card->isGigantic() &&
        isset($expeditionsBoosts[$player->getId()][$oppositeExpedition])
      ) {
        $boostExpedition = $oppositeExpedition;
      }
      if ($card->getType() == CHARACTER && isset($expeditionsBoosts[$player->getId()][$boostExpedition])) {
        $this->insertAsChild(FT::GAIN($card->getId(), BOOST, $expeditionsBoosts[$player->getId()][$boostExpedition]));
        unset($expeditionsBoosts[$player->getId()][$boostExpedition]);
        Globals::setNextCharacterInExpeditionBoost($expeditionsBoosts);
      }


      $listenerArgs = [
        'playCard' => true,
        'cardId' => $card->getId(),
        'cardType' => $card->getType(),
        'additionalType' => $card->getAdditionalType(),
        'from' => 'invoke',
        'to' => $location,
        'locationPId' => $invokePId,
        'gigantic' => $card->isGigantic(),
        'token' => true,
        'invoked' => $this->resolveTokenType(),
      ];
      $sourceId = $this->getSourceId();
      if (!is_null($sourceId)) {
        $listenerArgs['sourceId'] = $sourceId;
      }
      $this->checkAfterListeners($player, $listenerArgs);
      if (!$this->getArg('moreThan1') && $i == 0) {
        $this->checkAfterListeners($player, $listenerArgs, true, 'InvokeTokenOnce');
      }
    }
    if ($this->getN() <= 0) {
      $this->resolveAction(['']);
    } else {
      $this->resolveAction([$card->getId()]);
    }
  }
}
