<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Globals;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;
use ALT\Helpers\FT;
use ALT\Core\Engine;

class MoveExpedition extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_MOVE_EXPEDITION;
  }

  public function getDescription()
  {
    return clienttranslate('Move expedition');
  }

  protected $args = [
    'expedition' => [],
    'n' => 1,
    'pId' => null,
    'force' => false,
    'forceExpedition' => null,
    'winningBiomes' => [],
    'moveOtherExpedition' => false,
    'ascended' => false,
    'skipGigantic' => false,
  ];

  public function getSides()
  {
    $expeditions = $this->getArg('expedition');
    if (empty($expeditions)) {
      $expeditions = STORMS;
    }

    if (is_string($expeditions)) {
      $expeditions = [$expeditions];
    }

    foreach ($expeditions as &$expe) {
      if ($expe == EFFECT) {
        $expe = $this->getSource()->getLocation();
      } elseif ($expe == 'fromEvent') {
        // Resolve expedition from the triggering event context.
        $event = $this->getEventRecursive();
        if (!is_null($event) && in_array($event['method'], ['LeaveExpedition', 'LeaveLandmark']) && isset($event['from'])) {
          $expe = $event['from'];
        } elseif (!is_null($event) && $event['method'] == 'MoveExpedition' && isset($event['expedition'])) {
          $expe = $event['expedition'];
        }
      }
    }

    return $expeditions;
  }

  public function argsMoveExpedition()
  {
    $sides = $this->getSides();
    $n = $this->getArg('n');
    $forceExpedition = $this->getArg('forceExpedition');
    if (!is_null($forceExpedition)) {
      $expeditions = $forceExpedition;
    } else {
      $forcedPId = $this->getArg('pId');
      if ($forcedPId == OPPONENT) {
        $forcedPId = Players::getNextId(Players::getActive());
      } elseif ($forcedPId == ME) {
        $forcedPId = Players::getActiveId();
      }
      $blocked = Players::getBlockedExpeditions();

      $expeditions = [];
      foreach ($blocked as $pId => $statuses) {
        if (!is_null($forcedPId) && $forcedPId != $pId) {
          continue;
        }
        foreach ($statuses as $side => $isBlocked) {
          if (in_array($side, $sides) && ($n < 0 || !$isBlocked)) {
            $expeditions[] = [$pId, $side];
          }
        }
      }
    }
    return [
      'forceExpedition' => $this->getArg('forceExpedition'),
      'expeditions' => $expeditions,
      'descSuffix' => $n < 0 ? "backward" : "",
    ];
  }

  public function isDoable($player)
  {
    $args = $this->argsMoveExpedition();
    return !empty($args['expeditions']);
  }

  public function isAutomatic($player = null)
  {
    $args = $this->argsMoveExpedition();
    return count($args['expeditions']) == 1;
  }

  public function stMoveExpedition()
  {
    if (Globals::isTieBreakerMode()) {
      Notifications::message(clienttranslate('In tie-breaker this power has no effect.'), []);
      $this->resolveAction(null);
      return;
    }

    if (!is_null($this->getArg('forceExpedition'))) {
      return $this->getArg('forceExpedition');
    }

    $args = $this->argsMoveExpedition();
    if (count($args['expeditions']) == 1) {
      return [$args['expeditions'][0]];
    }
  }

  public function actMoveExpedition($expe)
  {
    $args = $this->argsMoveExpedition();
    $gigantic = false;
    if (!in_array($expe, $args['expeditions'])) {
      throw new \Bga\GameFramework\VisibleSystemException('Invalid expedition all. Should not happen');
    }

    if (!is_null($this->getSource()) && $this->getSource()->isGigantic()) {
      $gigantic = true;
    }

    // Combo Diocles & eat me energy bars
    $event = $this->getEvent();
    if (!is_null($event) && $event['method'] == 'LeaveExpedition' && $event['gigantic'] == true) {
      $gigantic = true;
    }

    if (!is_null($this->getArg('forceExpedition'))) {
      $expe = $this->getArg('forceExpedition');
    }

    $pId = $expe[0];
    $expedition = $expe[1];
    // throw new \feException("titi");
    $token = $expedition == STORM_LEFT ? HERO : COMPANION;
    $source = $this->getSource();
    $n = $this->getArg('n');
    $player = Players::get($pId);
    $winningBiomes = $this->getArg('winningBiomes');
    $ascended = $player->isAscended($expedition);

    // Rune's testament / Pegasus / Eris (non-dusk forced moves from spells and effects)
    if ($this->getArg('force') === false) {
      $actionInsteadAdvance = Players::getActionInsteadOfAdvance();
      if ($n > 0 && !empty($actionInsteadAdvance[$pId][$expedition] ?? [])) {
        $nodes = [];
        $forcedMoveArgs = [
          'pId' => $pId,
          'expedition' => [$expedition],
          'force' => true,
          'forceExpedition' => [$pId, $expedition],
          'winningBiomes' => $winningBiomes,
          'ascended' => $ascended,
        ];
        if (!in_array('ErisCommon', $actionInsteadAdvance[$pId][$expedition]) && !in_array('ErisRare', $actionInsteadAdvance[$pId][$expedition]) && !in_array('PegasusCommon', $actionInsteadAdvance[$pId][$expedition])) {
          $nodes[] = FT::ACTION(MOVE_EXPEDITION, array_merge($forcedMoveArgs, ['n' => $n]), ['pId' => $pId]);
        }
        foreach ($actionInsteadAdvance[$pId][$expedition] as $cId => $action) {
          $triggeredCard = Cards::get($cId);
          if ($action == 'draw2') {
            $nodes[] =
              FT::SEQ(
                FT::ACTION(DISCARD, ['cardId' => $cId], ['sourceId' => $cId]),
                FT::ACTION(DRAW, ['players' => ME, 'n' => 2], ['pId' => $pId, 'sourceId' => $cId])
              );
          } elseif ($action == 'look4') {
            $nodes[] =  FT::SEQ(
              FT::ACTION(DISCARD, ['cardId' => $cId], ['sourceId' => $cId]),
              FT::ACTION(SPECIAL_EFFECT, ['effect' => 'RunesTestamentLook4'], ['pId' => $pId, 'sourceId' => $cId])
            );
          } elseif ($action == 'ErisCommon') {
            if (!$triggeredCard->isGigantic()) {
              $nodes[] = FT::ACTION(ROLL_DIE, [
                'effect' => [
                  '1-3' => FT::ACTION(MOVE_EXPEDITION, array_merge($forcedMoveArgs, ['n' => $n]), ['pId' => $pId]),
                  '4+' => FT::ACTION(MOVE_EXPEDITION, array_merge($forcedMoveArgs, ['n' => $n + 1]), ['pId' => $pId])
                ]
              ], ['sourceId' => $cId, 'pId' => $pId]);
            } else {
              $nodes[] = FT::ACTION(MOVE_EXPEDITION, array_merge($forcedMoveArgs, ['n' => $n]), ['pId' => $pId]);
            }
          } elseif ($action == 'ErisRare') {
            if (!$triggeredCard->isGigantic()) {
              $nodes[] = FT::ACTION(ROLL_DIE, [
                'effect' => [
                  '2-3' => FT::ACTION(MOVE_EXPEDITION, array_merge($forcedMoveArgs, ['n' => $n]), ['pId' => $pId]),
                  '4+' => FT::ACTION(MOVE_EXPEDITION, array_merge($forcedMoveArgs, ['n' => $n + 1]), ['pId' => $pId])
                ]
              ], ['sourceId' => $cId, 'pId' => $pId]);
            } else {
              $nodes[] = FT::ACTION(MOVE_EXPEDITION, array_merge($forcedMoveArgs, ['n' => $n]), ['pId' => $pId]);
            }
          } elseif ($action == 'PegasusCommon') {
            $pegasusN = (empty($winningBiomes) && $ascended) ? $n + 1 : $n;
            $nodes[] = FT::ACTION(MOVE_EXPEDITION, array_merge($forcedMoveArgs, ['n' => $pegasusN]), ['pId' => $pId, 'sourceId' => $cId]);
          }
        }
        if (!empty($nodes)) {
          $this->insertAsChild(
            count($nodes) == 1
              ? $nodes[0]
              : ['type' => NODE_XOR, 'childs' => $nodes, 'pId' => $pId]
          );
        }

        $this->resolveAction(['']);
        return;
      }
    }
    $winningBiomes = $this->getArg('winningBiomes');
    if (($n > 0 && !Players::hasOpponentBlockMoveExpedition($player, $expedition)) || $n < 0) {
      $moved = $player->advanceStorm($token, $winningBiomes, $n, false, true, $source);
    } else {
      $moved = false;
    }
    if ($moved) {
      $expeditionMoves = Globals::getExpeditionMoves();
      $expeditionMoves[$player->getId()][$expedition] = ($expeditionMoves[$player->getId()][$expedition] ?? 0) + $n;
      Globals::setExpeditionMoves($expeditionMoves);

      $this->checkAfterListeners($player, ['moveExpedition' => $n, 'ascended' => $ascended, 'expedition' => $expedition]);
      if ($this->getArg('moveOtherExpedition') === true) {
        // Cable Car / Western Wind: move the other expedition the opposite way.
        // Forward moves must go through MOVE_EXPEDITION so actionInsteadAdvance
        // (Rune's Testament, Eris, Pegasus, …) can offer a replacement.
        $otherExpedition = $expedition == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;
        $otherN = $n * -1;
        if ($otherN > 0 && !Players::hasOpponentBlockMoveExpedition($player, $otherExpedition)) {
          $ctx = ['pId' => $pId];
          if (!is_null($source)) {
            $ctx['sourceId'] = $source->getId();
          }
          $this->insertAsChild(FT::ACTION(MOVE_EXPEDITION, [
            'n' => $otherN,
            'pId' => $pId,
            'force' => false,
            'forceExpedition' => [$pId, $otherExpedition],
            'winningBiomes' => $winningBiomes,
          ], $ctx));
        } elseif ($otherN < 0) {
          $moved = $player->advanceStorm($token == HERO ? COMPANION : HERO, $winningBiomes, $otherN, false, true, $source);
          $expeditionMoves = Globals::getExpeditionMoves();
          $expeditionMoves[$player->getId()][$otherExpedition] = ($expeditionMoves[$player->getId()][$otherExpedition] ?? 0) + $otherN;
          Globals::setExpeditionMoves($expeditionMoves);
          $this->checkAfterListeners($player, ['moveExpedition' => $otherN, 'ascended' => $player->isAscended($otherExpedition), 'expedition' => $otherExpedition]);
        }
    }

    if ($gigantic && !$this->getArg('skipGigantic')) {
      // we must move the other expedition
      $expedition = $expedition == STORM_LEFT ? STORM_RIGHT : STORM_LEFT;

      $token = $expedition == STORM_LEFT ? HERO : COMPANION;
      if (($n > 0 && !Players::hasOpponentBlockMoveExpedition($player, $expedition)) || $n < 0) {
        $moved = $player->advanceStorm($token, $winningBiomes, $n, false, true, $source);
        if ($moved) {
          $expeditionMoves = Globals::getExpeditionMoves();
          $expeditionMoves[$player->getId()][$expedition] = ($expeditionMoves[$player->getId()][$expedition] ?? 0) + $n;
          Globals::setExpeditionMoves($expeditionMoves);

          $this->checkAfterListeners($player, ['moveExpedition' => $n, 'ascended' => $player->isAscended($expedition), 'expedition' => $expedition]);
        }
      }
    }
  }
}
}