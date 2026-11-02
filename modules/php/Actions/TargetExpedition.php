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

class TargetExpedition extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_TARGET_EXPEDITION;
  }

  protected $args = ['n' => 1, 'players' => ALL, 'otherThanMe' => false];

  public function getDescription()
  {
    $msg = clienttranslate('Target ${n} expeditions to ${effect_desc}');
    if ($this->getArg('players') == OPPONENT) {
      $msg = clienttranslate('Target ${n} opponent\'s expeditions to ${effect_desc}');
    }
    return [
      'log' => $msg,
      'args' => [
        'n' => $this->getCtxArg('n') ?? 1,
        'effect_desc' => Engine::buildTree($this->getCtxArg('effect'))->getDescription(),
      ],
    ];
  }

  public function argsTargetExpedition()
  {
    $expeditions = [];
    $expeditionType = $this->getCtxArgs()['type'] ?? null;
    $stormMoves = Globals::getStormMoves();
    $winners = Players::getWinningPlayerByStorms();
    $otherThanMe = $this->getArg('otherThanMe');

    $sourceExpedition = null;
    if ($otherThanMe) {
      $source = $this->getSource();
      $sourceLocation = $source->getLocation();
      $sourcePId = $source->getPId();
    }

    if ($this->getArg('players') == ALL) {
      $players = Players::getAll();
    } elseif ($this->getArg('players') == ME) {
      $players = [$this->getPlayer()->getId() => $this->getPlayer()];
    } else {
      $players = [Players::getNext($this->getPlayer())->getId() => Players::getNext($this->getPlayer())];
    }

    foreach ($players as $pId => $player) {
      foreach (STORMS as $storm) {
        if ($expeditionType == 'ahead' && $winners[$storm] != $pId) {
          continue;
        }
        if ($expeditionType == 'movedThisDusk' && (($stormMoves[$pId][$storm]['moves'] ?? 0) <= 0)) {
          continue;
        }
        if ($otherThanMe === true && $sourceLocation == $storm && $sourcePId == $pId) {
          continue;
        }
        $expeditions[] = $pId . '-' . $storm;
      }
    }
    return ['expeditions' => $expeditions, 'n' => $this->getArg('n')];
  }

  public function isDoable($player)
  {
    return count($this->argsTargetExpedition()['expeditions']) > 0;
  }

  public function stTargetExpedition()
  {
    if (($this->getCtxArg('expedition') ?? '') == 'all') {
      return ['all'];
    }
  }

  public function actTargetExpedition($expedition)
  {
    $player = $this->getPlayer();
    $args = $this->argsTargetExpedition();

    if ($expedition == 'all') {
      $expedition = $args['expeditions'];
    }
    foreach ($expedition as $index => $exp) {
      $expeditions = explode('-', $exp);
      if (!in_array($exp, $args['expeditions'])) {
        throw new \BgaVisibleSystemException('Invalid target expedition. Should not happen');
      }

      $pId = $expeditions[0];

      $node = $this->getArg('effect');
      $node['sourceId'] = $this->getSourceId();
      $node['args']['player'] = $pId;
      $node['args']['expedition'] = $expeditions[1];
      $node['args']['forceExpedition'] = [$pId, $expeditions[1]];
      if (count($expedition) > 1 && $index > 0) {
        $node['args']['moreThan1'] = true;
      }

      if (isset($node['childs'])) {
        foreach ($node['childs'] as &$child) {
          $child['sourceId'] = $this->getSourceId();
          $child['args']['player'] = $pId;
          $child['args']['expedition'] = $expeditions[1];
          $child['args']['forceExpedition'] = [$pId, $expeditions[1]];
        }
      }
      $this->pushParallelChild($node);
    }


    return [$expedition];
  }
}
