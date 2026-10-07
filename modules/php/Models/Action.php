<?php

namespace ALT\Models;

use ALT\Core\Engine;
use ALT\Core\Game;
use ALT\Core\Globals;
use ALT\Core\Notifications;
use ALT\Managers\Cards;
use ALT\Managers\Players;
use ALT\Helpers\Log;
use ALT\Helpers\FlowConvertor;

/*
 * Action: base class to handle atomic action
 */

class Action
{
  protected $ctx = null; // Contain ctx information : current node of flow tree
  protected $description = '';
  public function __construct(&$ctx)
  {
    $this->ctx = &$ctx;
  }

  public function isDoable($player)
  {
    return true;
  }

  public function isOptional($player)
  {
    return false;
  }

  public function canPassAction($player)
  {
    return $this->isOptional($player);
  }

  public function isIndependent($player = null)
  {
    return false;
  }

  public function isAutomatic($player = null)
  {
    return false;
  }

  public function isIrreversible($player = null)
  {
    return false;
  }

  public function isMandatory()
  {
    return false;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function getPlayer()
  {
    $pId = $this->ctx->getPId() ?? Players::getActiveId();
    return Players::get($pId);
  }

  public function getState()
  {
    return null;
  }

  /**
   * Syntaxic sugar
   */
  public function &getCtx()
  {
    return $this->ctx;
  }

  public function getCtxArgs()
  {
    if ($this->ctx == null) {
      return [];
    } elseif (is_array($this->ctx)) {
      return $this->ctx;
    } else {
      return $this->ctx->getArgs() ?? [];
    }
  }
  public function getCtxArg($v)
  {
    return $this->getCtxArgs()[$v] ?? null;
  }

  protected $args = []; // Contain default expected value for args
  public function getArg($v)
  {
    $t = $this->getCtxArg($v) ?? null;
    if (is_null($t)) {
      if (array_key_exists($v, $this->args)) {
        $t = $this->args[$v];
      } else {
        throw new \BgaVisibleSystemException('Trying to get value of an undefined arg without any default value : ' . $v . ' action' . $this->getClassName());
      }
    }
    return $t;
  }

  public function getEvent()
  {
    return $this->ctx->getEvent();
  }

  public function getEventRecursive($ctx = null)
  {
    if (is_null($ctx)) {
      $ctx = $this->ctx;
    }
    if (is_null($ctx->getEvent())) {
      if (!is_null($ctx->getParent())) {
        return $this->getEventRecursive($ctx->getParent());
      }
      return null;
    }
    return  $ctx->getEvent();
  }

  public function getSourceId()
  {
    return $this->ctx->getSourceId();
  }

  public function getSource()
  {
    $sourceId = $this->ctx->getSourceId();
    return is_null($sourceId) ? null : Cards::get($sourceId);
  }

  public function resolveAction($args = [], $checkpoint = false)
  {
    $player = Players::getActive();
    $args['automatic'] = $this->isAutomatic($player);
    Engine::resolveAction($args, $checkpoint, $this->ctx);
    Engine::proceed();
  }

  /**
   * Insert flow as child of current node
   */
  public function insertAsChild($flow)
  {
    if (empty($flow)) {
      return;
    }
    Engine::insertAsChild($flow, $this->ctx);
  }

  /**
   * Insert childs on the next upcoming afterFinishingAction node
   */
  public function pushAfterFinishingChilds($childs)
  {
    Engine::pushAfterFinishingChilds($childs);
  }

  /**
   * Insert childs as parallel node childs
   */
  public function pushParallelChild($node)
  {
    $this->pushParallelChilds([$node]);
  }

  public function pushParallelChilds($childs)
  {
    Engine::insertOrUpdateParallelChilds($childs, $this->ctx);
  }

  public function updateParallelChilds($attributes)
  {
    Engine::updateParallelChilds($attributes, $this->ctx);
  }

  public function updateAfterFinishingChilds($attributes)
  {
    Engine::updateAfterFinishingChilds($attributes, $this->ctx);
  }

  /**
   * Given bonuses, compute the flow and insert them as childs (or on insertAfterFinishing node)
   */
  public function insertBonusesFlow($bonuses, $source = '', $sourceType = null, $sourceId = null)
  {
    if (empty($bonuses)) {
      return;
    }

    if (isset($bonuses[0]['type']) || isset($bonuses['type'])) {
      // we already are receiving a node
      $immediate = $bonuses;
      $after = [];
    } else {
      // list($immediate, $after) = FlowConvertor::getFlow($bonuses, $source, $sourceType, $sourceId);
    }
    $this->pushParallelChilds($immediate);
    $this->pushAfterFinishingChilds($after);
  }

  public function getClassName()
  {
    $classname = get_class($this);
    if ($pos = strrpos($classname, '\\')) {
      return substr($classname, $pos + 1);
    }
    return $classname;
  }

  /**
   * Queue passive reactions for an action event (resolved after the current action finishes).
   *
   * Merges standard event fields (`pId`, `action`, `method`) with `$args` (e.g. `cardId`, `to`,
   * `playCard`), finds listening cards via Cards::getReaction(), and pushes ACTIVATE_CARD nodes on
   * the after-finishing stack. Condition checks on those passives run when each reaction executes,
   * not at registration time—see Conditions::isStillSameLocation().
   *
   * @param string             $method           Current action method name (usually unused when $overrideMethod is set).
   * @param \ALT\Models\Player $player
   * @param array              $args             Event payload (cardId, to, playCard, …).
   * @param string|null        $overrideMethod   Use as `action`/`method` instead of this action class (e.g. MoveCard + defect).
   */
  protected function checkListeners($method, $player, $args = [], $overrideMethod = null)
  {
    $event = array_merge(
      [
        'pId' => $player->getId(),
        'type' => 'action',
        'action' => $overrideMethod ?? $this->getClassName(),
        'method' => $overrideMethod ?? $method,
      ],
      $args
    );

    $reaction = Cards::getReaction($event);
    // throw new \feException(print_r($reaction));
    if (empty($reaction)) {
      return;
    }

    $reaction = $this->filterGroupedReactions($reaction, $event);

    // Split reactions flagged as immediate (resolved inline within the current action's
    // branch) from the default deferred ones (resolved on the after-finishing node once
    // the whole current phase/action has finished).
    $immediate = [];
    $deferred = [];
    foreach ($reaction as $child) {
      if (($child['immediate'] ?? false) === true) {
        unset($child['immediate']);
        $immediate[] = $child;
      } else {
        $deferred[] = $child;
      }
    }

    if (!empty($immediate)) {
      $this->pushParallelChilds($immediate);
    }
    $this->pushAfterFinishingChilds($deferred);
  }

  /**
   * When an effect distributes several gains at once (eg. "Distribute 4 boosts...", see
   * FT::SEQ_DISTRIBUTE_GAINS), all its gains belong to the same gain event: only the first
   * reaction of a given listener is kept, so "gains 1 or more boosts" abilities trigger once
   * for the whole distribution instead of once per gain.
   */
  protected function filterGroupedReactions($reaction, $event)
  {
    if (empty($reaction) || is_null($event['gainGroup'] ?? null)) {
      return $reaction;
    }

    $group = $event['gainGroup'];

    return array_values(
      array_filter($reaction, function ($child) use ($group) {
        return !Engine::hasPendingGroupedReaction($child['args']['cardId'] ?? null, $group);
      })
    );
  }

  protected function logReactions($method, $player, $args = [], $overrideMethod = null)
  {
    $event = array_merge(
      [
        'pId' => $player->getId(),
        'type' => 'action',
        'action' => $overrideMethod == true ? $method : $this->getClassName(),
        'method' =>  $method,
      ],
      $args
    );
    // throw new \feException(print_r($event));
    $reaction = Cards::getReaction($event);
    // throw new \feException(print_r($reaction));
    // $this->pushParallelChilds($reaction);
    return $reaction;
  }

  /**
   * Fire “after” passives for this action (e.g. ChooseAssignment, InvokeToken, MoveCard).
   *
   * Cards declare listeners under effectPassive[$actionName]. This is the usual entry point
   * after something enters play or changes zone. Reactions are deferred via checkListeners().
   *
   * @param bool        $duringActionListener  When false, skip (legacy hook; rarely used).
   * @param string|null $overrideMethod        Passed to checkListeners() as the trigger name.
   */
  public function checkAfterListeners($player, $args = [], $duringActionListener = true, $overrideMethod = null)
  {
    if ($duringActionListener) {
      $this->checkListeners($this->getClassName(), $player, $args, $overrideMethod);
    }
    // removed, not sure it's consistent in Altered
    // $this->checkListeners('ImmediatelyAfter' . $this->getClassName(), $player, $args);
    // $this->checkListeners('After' . $this->getClassName(), $player, $args);
  }

  public function checkImmediateListeners($player, $args = [], $duringActionListener = true)
  {
    $event = array_merge(
      [
        'pId' => $player->getId(),
        'type' => 'action',
        'action' => 'Immediate' . $this->getClassName(),
        'method' => 'Immediate' . $this->getClassName(),
      ],
      $args
    );

    $reaction = Cards::getReaction($event, true, false);
    if ($reaction  !== null) {
      // 227852 - Give Fate a Twist + Wayfarer interaction: 
      // Wayfarer’s ImmediateRollDie triggers a RevealTop effect, which triggers a RevealTop listener on the same card. 
      // To allow player to choose to use the Fate roll +1, it needs to be offered as parallel actions with Wayfarer, not in a sequence.
      if (count($reaction) > 1) {
        Engine::insertAtRoot([
          'type' => NODE_PARALLEL,
          'pId' => $event['pId'],
          'noIndependent' => true,
          'childs' => $reaction,
        ], false);
      } else {
        Engine::insertAtRoot(['type' => NODE_SEQ, 'childs' => $reaction], false);
      }
    }
  }

  public function checkModifiers($method, &$data, $name, $player, $args = [])
  {
    $args[$name] = $data;
    if (!isset($args['actionCardId'])) {
      $args['actionCardId'] = $this->ctx != null ? $this->ctx->getCardId() : null;
    }
    Cards::applyEffects($player, $method, $args);
    $data = $args[$name];
  }

  public function checkCostModifiers(&$costs, $player, $args = [])
  {
    $this->checkModifiers('computeCosts' . $this->getClassName(), $costs, 'costs', $player, $args);
  }

  public function checkArgsModifiers(&$actionArgs, $player, $args = [])
  {
    $this->checkModifiers('computeArgs' . $this->getClassName(), $actionArgs, 'actionArgs', $player, $args);
  }

  /**
   * Update the args of current node
   * @param array $args : the keys/values that needs to get updated
   * Warning: resolve action must be call on the side
   */
  public function duplicateAction($args = [], $checkpoint = false)
  {
    // Duplicate the node and update the args
    $node = $this->ctx->toArray();
    $node['type'] = \NODE_LEAF;
    $node['childs'] = [];
    $node['args'] = array_merge($node['args'], $args);
    $node['duplicate'] = true;
    unset($node['mandatory']); // Weird edge case
    $node = Engine::buildTree($node);
    // Insert it as a brother of current node and proceed
    $this->ctx->insertAsBrother($node);
    Engine::save();

    if ($checkpoint) {
      Engine::checkpoint();
    }
    // Engine::proceed();
  }

  /**
   * Bind a resolved card (or target choice) into an effect-flow tree before execution.
   *
   * Card definitions often use placeholders in flow nodes (`ME`, `EFFECT`, `mana`) or leave
   * `cardId` empty until the player picks a target. After Target::actTarget() or Spend::actSpend(),
   * the chosen card’s id, zone, owner, and source must be written into the nested actions (GAIN,
   * DISCARD, MOVE_CARD, …) that will run next, so those nodes know which card they are referring to.
   *
   * This function walks the tree recursively (`childs`, `args.effect`, `args.oppositeEffect`, `cost branches`...) 
   * and updates each node in place, returning the modified tree.
   *
   * Rules while walking down the tree:
   * - Every node gets `sourceId` (card that caused the effect).
   * - `args.cardId` / `cardFrom` / `ownerId` are set from the parameters unless if there's already a
   *   placeholder (`ME`, `mana`) or if forced to use `EFFECT` through $preserveEffectPlaceholder.
   * - `TARGET` nodes that need a prior pick (`excludePreviousTarget`, `compareTargetBiome`
   *   with `source` => `cardId`, `maxHandCost` => `discard2`) store it in `args.cardId` for ctx.
   *   `cardId` is not propagated into their nested `effect` (TARGET: Guiding Ocelot, Sabotage, …;
   *   SPEND: nested `GAIN(EFFECT)` must still refer to the trigger event card).
   * - `pId` === `'owner'` is replaced with $ownerId (controller of the targeted card).
   * - If the card comes from an expedition, `wasGigantic` is stored on the node when relevant.
   *
   * @param array       $node                         Engine node array (leaf or subtree root).
   * @param int|int[]   $cardId                       Resolved target id(s).
   * @param string      $cardFrom                     Zone of the target (e.g. stormLeft); may be overridden by nested `targetLocation`.
   * @param int         $sourceId                     Id of the card that owns/triggered the effect.
   * @param int         $ownerId                      Player id controlling the targeted card.
   * @param bool        $preserveEffectPlaceholder    When true, keep `cardId` === EFFECT so nested effects still refer to the event card (see Spend).
   *
   * @return array The same tree shape with ids/zones filled in for execution.
   */
  public function updateCardId($node, $cardId, $cardFrom, $sourceId, $ownerId, $preserveEffectPlaceholder = false)
  {
    $isTargetAction = (($node['action'] ?? null) === \TARGET);
    $isSpendAction = (($node['action'] ?? null) === \SPEND);
    $childPreserveEffect = $preserveEffectPlaceholder || $isSpendAction;

    $cid = $node['args']['cardId'] ?? null;
    // Only preserve EFFECT when explicitly requested (e.g. nested GAIN(EFFECT) after Spend).
    // Do not keep EFFECT on Spend's own cardId when Target binds the picked card.
    // This caused bugs where Spend's nested effects couldn't tell which card was spent, and where TARGET: Guiding Ocelot's nested effect couldn't tell which card was picked.
    $keepPlaceholder =
      $cid === ME ||
      $cid === MANA ||
      ($preserveEffectPlaceholder && $cid === EFFECT);
    $needsPriorTargetCtx = $isTargetAction && $this->targetNeedsPriorSelectionCtx($node);
    if (!$isTargetAction || $needsPriorTargetCtx) {
      if (!isset($node['args']['cardId']) || !$keepPlaceholder) {
        if (!$isTargetAction || $cardId !== null) {
          $node['args']['cardId'] = $cardId;
          $node['args']['cardFrom'] = $cardFrom;
          $node['args']['ownerId'] = $ownerId;
          if (!is_null($cardId) && !is_array($cardId) && in_array($cardFrom, STORMS)) {
            $targetCard = Cards::get($cardId, false);
            if ($targetCard !== null) {
              $node['args']['wasGigantic'] = $targetCard->isGigantic();
            }
          }
        }
      }
    }

    if (isset($node['pId']) && $node['pId'] == 'owner') {
      $node['pId'] = $ownerId;
    }

    if (isset($node['1-3'])) {
      $node['1-3'] = $this->updateCardId($node['1-3'], $cardId, $cardFrom, $sourceId, $ownerId, $childPreserveEffect);
    }
    if (isset($node['4+'])) {
      $node['4+'] = $this->updateCardId($node['4+'], $cardId, $cardFrom, $sourceId, $ownerId, $childPreserveEffect);
    }

    $node['sourceId'] = $sourceId;

    $effectPropagateId = $isTargetAction ? null : $cardId;

    if (isset($node['args']['effect']) && is_array($node['args']['effect'])) {
      $childCardFrom = isset($node['args']['effect']['args']['targetLocation']) 
        ? $node['args']['effect']['args']['targetLocation'] 
        : $cardFrom;
          // Under SPEND, keep nested GAIN(EFFECT) on the listener event card (Guiding Ocelot).
      $effectPreserveEffect = $isSpendAction ? $childPreserveEffect : $preserveEffectPlaceholder;
      $node['args']['effect'] = $this->updateCardId($node['args']['effect'], $effectPropagateId, $childCardFrom, $sourceId, $ownerId, $effectPreserveEffect);
    }
    if (isset($node['args']['oppositeEffect']) && is_array($node['args']['oppositeEffect'])) {
      $node['args']['oppositeEffect'] = $this->updateCardId(
        $node['args']['oppositeEffect'],
        $cardId,
        $cardFrom,
        $sourceId,
        $ownerId,
        $childPreserveEffect
      );
    }
    if (isset($node['childs'])) {
      $node['childs'] = array_map(function ($child) use ($cardId, $cardFrom, $sourceId, $ownerId, $childPreserveEffect) {
        return $this->updateCardId($child, $cardId, $cardFrom, $sourceId, $ownerId, $childPreserveEffect);
      }, $node['childs']);
    }


    return $node;
  }

  /**
   * Nested TARGET that must read the previous pick from ctx (`getCtxArg('cardId')`).
   */
  private function targetNeedsPriorSelectionCtx(array $node)
  {
    $args = $node['args'] ?? [];
    if (!empty($args['excludePreviousTarget'])) {
      return true;
    }
    if (($args['maxHandCost'] ?? null) === 'discard2') {
      return true;
    }
    $compare = $args['compareTargetBiome'] ?? null;
    return is_array($compare) && ($compare['source'] ?? null) === 'cardId';
  }
}
