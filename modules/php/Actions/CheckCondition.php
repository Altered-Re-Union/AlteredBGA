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
use ALT\Helpers\Conditions;
use ALT\Helpers\Utils;
use ALT\Models\Player;

class CheckCondition extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_CHECK_CONDITION;
  }

  protected $args = ['condition' => null, 'effect' => null, 'oppositeEffect' => null, 'previousEvent' => false, 'ignoreDeck' => false];

  private function isFlowEffect($effect): bool
  {
    return is_array($effect) && (isset($effect['action']) || isset($effect['type']) || isset($effect['childs']));
  }

  public function getConditions()
  {
    $conditions = $this->getCtxArg('conditions');
    if (!is_null($conditions)) {
      return $conditions;
    } else {
      $cond = $this->getArg('condition');
      return is_array($cond) ? $cond : [$cond];
    }
  }

  public function getDescription()
  {
    $conditions = $this->getConditions();
    $desc = $this->getCtxArg('description');
    foreach ($conditions as $condition) {
      if ($condition == 'hasBoost:4:LTE') {
        return $desc = ['log' => clienttranslate('Check if there are less than 4 <BOOST>'), 'args' => []];
      }
    }
    if ($desc == null) {
      $effect = $this->getCtxArg('effect');
      if (!$this->isFlowEffect($effect)) {
        return ['log' => clienttranslate('if valid condition'), 'args' => []];
      }
      $flow = Engine::buildTree($effect);
      $args['action0'] = ['log' => clienttranslate('if valid condition'), 'args' => []];
      $args['action1'] = $flow->getDescription();
      $desc = '${action0}: ${action1}';
      return [
        'log' => $desc,
        'args' => $args
      ];
    }
    return $desc;
  }

  public function isIndependent($player = null)
  {
    $cards = Cards::getPlayedCards(null);
    $conditions = $this->getConditions();
    foreach ($cards as $cId => $card) {
      $block = $card->getBlockAutomaticAction();
      if (isset($block[CHECK_CONDITION])) {

        if (!empty(array_intersect($block[CHECK_CONDITION], $conditions))) {
          return false;
        }
      }
    }

    return true;
  }

  public function isDoable($player)
  {
    return $this->checkCondition($player) || (!is_null($this->getArg('oppositeEffect')) && $this->getArg('oppositeEffect') != 'OPPOSITE');
  }

  protected function effectRequiresDeck($effect)
  {
    if (!is_array($effect)) {
      return false;
    }

    if (in_array($effect['action'] ?? null, [DRAW, RESUPPLY, DRAW_MANA], true)) {
      return true;
    }

    if (($effect['action'] ?? null) === SPECIAL_EFFECT) {
      $specialEffect = $effect['args']['effect'] ?? null;
      if (is_string($specialEffect)) {
        return in_array($specialEffect, [
          'revealTop',
          'drawReveal',
          'MindApotheosis',
          'RunesTestamentLook4',
          'boostedRevealBaseStat',
          'boostedRevealArtistSong',
          'boostedRevealRobotPermanent',
          'RomanticEncounter',
          'AuraqKibble',
        ], true);
      }
    }

    foreach ($effect['childs'] ?? [] as $child) {
      if ($this->effectRequiresDeck($child)) {
        return true;
      }
    }

    if (isset($effect['args']['effect']) && is_array($effect['args']['effect'])) {
      return $this->effectRequiresDeck($effect['args']['effect']);
    }

    return false;
  }

  protected function getDeckPlayer($player)
  {
    $source = $this->getSource();
    if (!is_null($source)) {
      return $source->getPlayer();
    }

    return $player;
  }

  public function checkCondition($player)
  {
    $source = $this->getSource();
    $event = ['pId' => $player->getId()];
    if ($this->getArg('previousEvent')) {
      $event = $this->getCtx()->toArray()['event'];
    }
    $card = $source ?? $player->getHero();
    $ctxArgs = $this->getCtxArgs();
    if (isset($ctxArgs['cardFrom'])) {
      $event['cardFrom'] = $ctxArgs['cardFrom'];
    }
     if (isset($ctxArgs['wasGigantic'])) {
      $event['wasGigantic'] = $ctxArgs['wasGigantic'];
    }
     if (!Conditions::check($ctxArgs, $card, $event)) {
      return false;
    }

    $effect = $this->getArg('effect');
    if (!$this->getArg('ignoreDeck') && $this->effectRequiresDeck($effect) && !$this->getDeckPlayer($player)->hasDeckCards()) {
      return false;
    }

    return true;
  }

  public function stCheckCondition()
  {
    $player = Players::getActive();

    $node = $this->getArg('effect');

    if ($this->checkCondition($player) === false) {
      if (!is_null($this->getArg('oppositeEffect')) && $this->getArg('oppositeEffect') != 'OPPOSITE') {
        $node = $this->getArg('oppositeEffect');
      } else {
        $this->resolveAction(['notMet']);
        return;
      }
    }

    // effect may be null (or a stray placeholder string): condition met, nothing to run
    if (!$this->isFlowEffect($node)) {
      $this->resolveAction(['met']);
      return;
    }

    if (isset($node['childs'])) {
      foreach ($node['childs'] as &$child) {
        $child['sourceId'] = $this->getSourceId();
      }
    }
    $node['sourceId'] = $this->getSourceId();
    $this->pushParallelChild($node);
    $this->resolveAction(['met']);
  }
}
