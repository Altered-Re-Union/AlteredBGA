<?php

namespace ALT\Core;

use ALT\Managers\Players;
use ALT\Managers\Actions;
use ALT\Managers\Scores;
use ALT\Helpers\Log;
use ALT\Helpers\QueryBuilder;
use ALT\Helpers\UserException;
use ALT\Managers\Cards;

/*
 * Engine: a class that allows to handle complex flow
 */

class Engine
{
  public static $tree = null;

  public static function boot()
  {
    $t = Globals::getEngine();
    self::$tree = self::buildTree($t);
    self::ensureSeqRootNode();
  }

  /**
   * Save current tree into Globals table
   */

  public static function save()
  {
    $t = self::$tree->toArray();
    Globals::setEngine($t);
  }

  /**
   * Ensure the root is a SEQ node to be able to insert easily in the current flow
   */
  protected static function ensureSeqRootNode()
  {
    if (!self::$tree instanceof \ALT\Core\Engine\SeqNode) {
      self::$tree = new \ALT\Core\Engine\SeqNode([], [self::$tree]);
      self::save();
    }
  }

  /**
   * Setup the engine, given an array representing a tree
   * @param array $t
   */
  public static function setup($t, $callback)
  {
    self::$tree = self::buildTree($t);
    self::save();
    Globals::setCallbackEngineResolved($callback);
    Globals::setEngineChoices(0);
    Globals::setEngineAutomatic(0);
    Log::enable(); // Enable log
    Log::startEngine();
  }

  /**
   * Convert an array into a tree
   * @param array $t
   */
  public static function buildTree($t)
  {
    $t['childs'] = $t['childs'] ?? [];
    $type = $t['type'] ?? (empty($t['childs']) ? NODE_LEAF : NODE_SEQ);

    $childs = [];
    foreach ($t['childs'] as $child) {
      $childs[] = self::buildTree($child);
    }

    $className = 'ALT\\Core\Engine\\' . ucfirst($type) . 'Node';
    unset($t['childs']);
    return new $className($t, $childs);
  }

  /**
   * Recursively compute the next unresolved node we are going to address
   */
  public static function getNextUnresolved()
  {
    return self::$tree->getNextUnresolved();
  }

  /**
   * Recursively compute the next undoable mandatory node, if any
   */
  public static function getUndoableMandatoryNode($player)
  {
    return self::$tree->getUndoableMandatoryNode($player);
  }

  /**
   * Proceed to next unresolved part of tree
   */
  public static function proceed($confirmedPartial = false, $isUndo = false)
  {
    $node = self::$tree->getNextUnresolved();
    // if ($node->toArray()['type'] != NODE_LEAF) {
    //   throw new \feException(print_r($node->toArray()));
    // }
    $player = Players::getActive();

    // Are we done ?
    if ($node == null) {
      // throw new \feException(print_r(Globals::getEngine()));
      // throw new \feException(print_r(debug_print_backtrace()));
      $skipped = Globals::getSkippedPlayers(); 
      // Effect-driven free plays (e.g. Wayfarer) never bump playedCards; only the Afternoon
      // assignment play / pass ends the turn. See ChooseAssignment::playCard.
      // if card was played or action passed, we are done
      if (
        (Globals::getDayPhase() === true && (Globals::getPlayedCards() != 0 || in_array(Globals::getActivePId(), $skipped))) ||
        Globals::getDayPhase() === false
      ) {
        if (Globals::getEngineChoices() == 0 || !Globals::isUndo() || (Globals::isUndo() && $player->getPref(OPTION_PLAYER_UNDO) == OPTION_PLAYER_UNDO_DISABLED)) {
          self::confirm(); // No choices were made => auto confirm
        } else {
          // Confirm/restart
          Game::get()->gamestate->jumpToState(ST_CONFIRM_TURN);
        }
        return;
      }

      // otherwise, insert again a choose assignment (e.g. after hero tap + Wayfarer free play)
      Globals::setPlayedForFree(false);
      self::insertAtRoot(
        $node = [
          'childs' => [
            [
              'action' => CHOOSE_ASSIGNMENT,
              'pId' => Globals::getActivePId(),
            ],
          ],
        ]
      );
      self::proceed();
      return;
    }

    $oldPId = Game::get()->getActivePlayerId();
    $pId = $node->getPId();
    // throw new \feException($pId);
    // if ($node->getType() == NODE_PARALLEL) {
    //   var_dump("new" . $pId);
    // }
    // Multi active node
    if ($pId == 'all') {
      Game::get()->gamestate->jumpToState(ST_RESOLVE_STACK);
      Game::get()->gamestate->setAllPlayersMultiactive();

      // Ensure no undo
      Log::checkpoint();
      Globals::setEngineChoices(0);
      Globals::setEngineAutomatic(0);

      // Proceed to do the action
      self::proceedToState($node, $isUndo);
      return;
    } elseif ($pId == 'nextPlayer') {
      $pId = Players::getNextId(Players::getActive());
    } elseif ($pId == 'active') {
      $pId = $oldPId;
    } elseif ($pId == 'source') {
      $pId = Cards::get($node->getSourceId())->getPId();
    }
    // throw new \feException(print_r($node->toArray()));
    // throw new \feException($oldPId . "- " . $pId);

    if (
      (Globals::isUndo() && ($player->getPref(OPTION_PLAYER_UNDO) == OPTION_PLAYER_UNDO_ENABLED || $player->getPref(OPTION_CONFIRM_UNDOABLE) == OPTION_CONFIRM_ENABLED)) &&
      $pId != null &&
      $oldPId != $pId &&
      (!$node->isIndependent(Players::get($pId)) && Globals::getEngineChoices() != 0) &&
      !$confirmedPartial
    ) {
      Game::get()->gamestate->jumpToState(ST_CONFIRM_PARTIAL_TURN);
      return;
    }


    $player = Players::get($pId);
    // Jump to resolveStack state to ensure we can change active pId
    if ($pId != null && $oldPId != $pId) {
      Game::get()->gamestate->jumpToState(ST_RESOLVE_STACK);
      Game::get()->gamestate->changeActivePlayer($pId);
    }

    if ($pId == null) {
      $player = Players::getActive();
    }

    if ($confirmedPartial || ($pId != null && $oldPId != $pId)) {
      Log::enable();
      Log::checkpoint();
      Globals::setEngineChoices(0);
      // Globals::setEngineAutomatic(0);
    }

    if ($node instanceof \ALT\Core\Engine\OrNode && isset($node->getArgs()['canReuse']) && $node->getArgs()['canReuse'] == true) {
      $newNode = $node->toArray();
      if (!isset($newNode['backupNode'])) {
        // first time, we put the backupinfo
        $newNode['backupNode'] = $newNode['childs'];
      } else {
        // we put the backup 
        $newNode['childs'] = $newNode['backupNode'];
        unset($newNode['choice']);
      }
      $node->replace(self::buildTree($newNode));
      self::save();
      $node = self::$tree->getNextUnresolved();
    }

    // If node with choice, switch to choice state
    $choices = $node->getChoices($player);
    $allChoices = $node->getChoices($player, true);
    // if ($node->toArray()['type'] != NODE_LEAF) {
    //   throw new \feException(Globals::getEngineChoices());
    //   throw new \feException(print_r($allChoices));
    // }
    // throw new \feException(print_r($choices));
    if (!empty($allChoices) && $node->getType() != NODE_LEAF) {
      // var_dump($choices);
      // Only one choice : auto choose
      $id = array_keys($choices)[0] ?? null;
      if (
        count($choices) == 1 &&
        count($allChoices) == 1 &&
        array_keys($allChoices) == array_keys($choices) &&
        ((!Globals::isUndo() || (Globals::isUndo() && $player->getPref(OPTION_PLAYER_UNDO) == OPTION_PLAYER_UNDO_DISABLED)) ||
          (
            (!$choices[$id]['irreversibleAction'] &&  !$choices[$id]['optionalAction']) ||
            (Globals::getEngineChoices() == 0 && !$choices[$id]['optionalAction'])
          )
        )
        && !($choices[$id]['forceManualChoice'] ?? false)
      ) {
        // var_dump(debug_print_backtrace());
        // var_dump(Globals::getEngineChoices());
        // throw new \feException(print_r($allChoices));
        // var_dump("titi", $allChoices);
        self::chooseNode($player, $id, true);
        // To see depending on survey + add player option?
        // } elseif (count($choices) == 1 && $id == PASS && (!Globals::isUndo() || (Globals::isUndo() && $player->getPref(OPTION_PLAYER_UNDO) == OPTION_PLAYER_UNDO_DISABLED))) {
        //   self::chooseNode($player, $id, true);
      } else {
        // Otherwise, go in the RESOLVE_CHOICE state
        // var_dump($pId);

        Game::get()->gamestate->jumpToState(ST_RESOLVE_CHOICE);
      }
    } else {
      // No choice => proceed to do the action
      self::proceedToState($node, $isUndo);
    }
  }

  public static function proceedToState($node, $isUndo = false)
  {
    $state = $node->getState();
    $args = $node->getArgs();
    $actionId = $node->getAction();
    // Do some pre-action code if needed and if we are not undoing to an irreversible node
    if ((!$isUndo || !$node->isIrreversible(Players::get($node->getPId()))) && $node->getFlag() != PRE_ACTION_DONE) {
      $interrupt = Actions::stPreAction($actionId, $node);
      $node->flagStPreAction();
      self::save();
      if ($interrupt === true) {
        // throw new \feException("titi");
        Engine::proceed();
        return;
      }
    }
    Game::get()->gamestate->jumpToState($state);
  }

  /**
   * Get the list of choices of current node
   */
  public static function getNextChoice($player = null, $displayAllChoices = false)
  {
    return self::$tree->getNextUnresolved()->getChoices($player, $displayAllChoices);
  }

  /**
   * Choose one option
   */
  public static function chooseNode($player, $nodeId, $auto = false)
  {
    $node = self::$tree->getNextUnresolved();
    $args = $node->getChoices($player);
    if (!isset($args[$nodeId])) {
      throw new \BgaVisibleSystemException('This choice is not possible');
    }

    if (!$auto) {
      Globals::incEngineChoices();
      Log::step();
    }

    if ($nodeId == PASS) {
      $nextUnresolved = $node;
      // throw new \feException(print_r($nextUnresolved->toArray()));
      if ($nextUnresolved instanceof \ALT\Core\Engine\ParallelNode) {
        // if we have a parallel node & multiple players id, we pass only the nodes of the player
        $nodes = $nextUnresolved->getChilds();
        $otherPIds = null;
        if (is_null($player)) {
          $player = Players::getActive();
        }

        foreach ($nodes as &$child) {
          if ($child->isResolved()) {
            continue;
          }

          if ($child->getPId() == $player->getId() || is_null($child->getPId())) {
            $child->resolve([PASS]);
          } elseif ($child->isDoable($player)) {
            $otherPIds = $child->getPId();
          }
        }
        // if there are other player, we switch context to the other player
        if (!is_null($otherPIds)) {
          $nextUnresolved->setInfo('pId', $otherPIds);
        } else {
          $node->resolve([PASS]);
        }
        self::save();
        self::proceed();
        return;
      }
      self::resolve(PASS);
      self::save();
      self::proceed();
      return;
    }
    $canReuse = $node->getArgs()['canReuse'] ?? false;

    if ($node->getChilds()[$nodeId]->isResolved() && !$canReuse) {
      throw new \BgaVisibleSystemException('Node is already resolved');
    }
    if ($canReuse) {
      $node->getChilds()[$nodeId]->unresolveAction();

      foreach ($node->getChilds()[$nodeId]->getChilds() as $cID => &$child) {
        $child->unresolveAction();
      }
    }
    // if the player is choosing for another player & it's an optional action, we shoudl update the current choice node
    if (($node->getChilds()[$nodeId]->getPId() ?? $player->getId()) != $player->getId() && $node->getChilds()[$nodeId]->isOptional(Players::get($node->getChilds()[$nodeId]->getPId()))) {
      $node->setInfo('pId', $node->getChilds()[$nodeId]->getPId());
    } else {
      $node->choose($nodeId, $auto);
    }

    self::save();
    self::proceed();
  }

  /**
   * Resolve the current unresolved node
   * @param array $args : store informations about the resolution (choices made by players)
   */
  public static function resolve($args = [])
  {
    $node = self::$tree->getNextUnresolved();
    $node->resolve($args);
    self::save();
  }

  /**
   * Resolve action : resolve the action of a leaf action node
   */
  public static function resolveAction($args = [], $checkpoint = false, &$node = null)
  {
    if (is_null($node)) {
      $node = self::$tree->getNextUnresolved();
    }
    if (!$node->isReUsable()) {
      $node->resolveAction($args);
      if ($node->isResolvingParent()) {
        $node->getParent()->resolve([]);
      }
    }

    self::save();

    if (!isset($args['automatic']) || $args['automatic'] === false) {
      Globals::incEngineChoices();
    } else {
      Globals::incEngineAutomatic();
    }
    if ($checkpoint) {
      self::checkpoint();
    }
  }

  public static function checkpoint()
  {
    $player = Players::getActive();
    $node = self::getUndoableMandatoryNode($player);
    if (!is_null($node) && $node->getPId() == $player->getId()) {
      throw new UserException(
        totranslate("You can't take an irreversible action if there is a mandatory undoable action pending")
      );
    }

    // TODO : flag the tree
    Globals::setEngineChoices(0);
    // Globals::setEngineAutomatic(0);
    Log::checkpoint();
  }

  /**
   * Insert a new node at root level at the end of seq node
   */
  public static function insertAtRoot($t, $last = true)
  {
    self::ensureSeqRootNode();
    $node = self::buildTree($t);
    if ($last) {
      self::$tree->pushChild($node);
    } else {
      self::$tree->unshiftChild($node);
    }
    self::save();
    return $node;
  }

  /**
   * Get the "next after finishing action node", create a new if needed
   */
  public static function getAfterFinishingNode()
  {
    self::ensureSeqRootNode();
    // Go through root children
    $childs = self::$tree->getChilds();
    for ($i = 0; $i < count($childs); $i++) {
      if ($childs[$i]->getFlag() == AFTER_FINISHING_ACTION && !$childs[$i]->isResolved()) {
        $infos = $childs[$i]->getInfos();
        if (!isset($infos['activePId'])) {
          $childs[$i]->setInfo('activePId', Players::getActiveId());
          self::save();
        }
        return $childs[$i];
      }
    }

    // Active player effects run in parallel first, then opponent effects in parallel.
    return self::insertAtRoot([
      'type' => NODE_SEQ,
      'flag' => \AFTER_FINISHING_ACTION,
      'childs' => [
        ['type' => NODE_PARALLEL, 'flag' => \AFTER_FINISHING_ACTIVE, 'childs' => []],
        ['type' => NODE_PARALLEL, 'flag' => \AFTER_FINISHING_OPPONENT, 'childs' => []],
      ],
    ]);
  }

  /**
   * Pick the parallel bucket (active or opponent) for an afterFinishing child.
   */
  protected static function getAfterFinishingParallelBucket($afterFinishingNode, $child)
  {
    $activePId = $afterFinishingNode->getInfos()['activePId'] ?? Players::getActiveId();
    
    $childPId = $child['pId'] ?? null;
    if (is_null($childPId) || $childPId === 'active') {
      $childPId = $activePId;
    }
    if ($childPId === 'source' && isset($child['sourceId'])) {
      $childPId = Cards::get($child['sourceId'])->getPId();
    }
    
    $targetFlag = $childPId != $activePId ? \AFTER_FINISHING_OPPONENT : \AFTER_FINISHING_ACTIVE;

    foreach ($afterFinishingNode->getChilds() as $parallelNode) {
      if ($parallelNode->getFlag() == $targetFlag) {
        return $parallelNode;
      }
    }

    throw new \BgaVisibleSystemException('AfterFinishing parallel bucket not found. Should not happen');
  }

  /**
   * Insert after finishing action
   */
  public static function pushAfterFinishingChilds($childs)
  {
    if (empty($childs)) {
      return;
    }

    $node = self::getAfterFinishingNode();
    foreach ($childs as $child) {
      $bucket = self::getAfterFinishingParallelBucket($node, $child);
      $bucket->pushChild(self::buildTree($child));
    }
    Engine::save();
  }

  /**
   * insertAsChild: turn the node into a SEQ if needed, then insert the flow tree as a child
   */
  public static function insertAsChild($t, &$node = null)
  {
    if (is_null($t)) {
      return;
    }
    if (is_null($node)) {
      $node = self::$tree->getNextUnresolved();
    }

    // If the node is an action leaf, turn it into a SEQ node first
    if ($node->getType() == NODE_LEAF) {
      $newNode = $node->toArray();
      $newNode['type'] = NODE_SEQ;
      $node = $node->replace(self::buildTree($newNode));
    }

    // Push child
    $node->pushChild(self::buildTree($t));
    self::save();
  }

  /**
   * insertOrUpdateParallelChilds:
   *  - if the node is a parallel node => insert all the nodes as childs
   *  - if one of the child is a parallel node => insert as their childs instead
   *  - otherwise, make the action a parallel node
   */

  public static function insertOrUpdateParallelChilds($childs, &$node = null)
  {
    if (empty($childs)) {
      return;
    }
    if (is_null($node)) {
      $node = self::$tree->getNextUnresolved();
    }

    if ($node->getType() == NODE_SEQ) {
      // search if we have children and if so if we have a parallel node
      foreach ($node->getChilds() as $child) {
        if ($child->getType() == NODE_PARALLEL) {
          foreach ($childs as $newChild) {
            $child->pushChild(self::buildTree($newChild));
          }
          self::save();
          return;
        }
      }

      $node->pushChild(
        self::buildTree([
          'type' => \NODE_PARALLEL,
          'childs' => $childs,
        ])
      );
    }
    // Otherwise, turn the node into a PARALLEL node if needed, and then insert the childs
    else {
      // If the node is an action leaf, turn it into a Parallel node first
      if ($node->getType() == NODE_LEAF) {
        $newNode = $node->toArray();
        $newNode['type'] = NODE_PARALLEL;
        $node = $node->replace(self::buildTree($newNode));
      }

      // Push childs
      foreach ($childs as $newChild) {
        $node->pushChild(self::buildTree($newChild));
      }
      self::save();
    }
  }

  // update the parallel node for speeific cases
  public static function updateParallelChilds($attributes, &$node = null)
  {
    if (empty($attributes)) {
      return;
    }
    if (is_null($node)) {
      $node = self::$tree->getNextUnresolved();
      if (is_null($node)) {
        // Nothing to do
        return;
      }
    }

    if ($node->getType() == NODE_PARALLEL) {
      foreach ($attributes as $attribute => $value) {
        $node->setInfo($attribute, $value);
      }
      self::save();
      return;
    } elseif ($node->getType() == NODE_SEQ) {
      foreach ($node->getChilds() as $child) {
        self::updateParallelChilds($attributes, $child);
      }
    }
  }


  public static function updateAfterFinishingChilds($attributes, &$node = null)
  {
    if (empty($attributes)) {
      return;
    }

    $afterFinishingNode = self::getAfterFinishingNode();
    $nodes = array_merge([$afterFinishingNode], $afterFinishingNode->getChilds());
    foreach ($attributes as $attribute => $value) {
      foreach ($nodes as $node) {
        $node->setInfo($attribute, $value);
      }
    }
    Engine::save();
  }

  public static function updateBrotherArgs($args)
  {
    $node = self::getNextUnresolved();
    foreach ($node->getParent()->getChilds() as $n => &$brother) {
      if ($brother == $node) {
        continue;
      }
      $newBrother = $brother->toArray();
      if (count($args) != 0) {
        $newBrother['args'] = $newBrother['args'] + array_pop($args);
      }
      $brother = $brother->replace(self::buildTree($newBrother));
    }
    self::save();
  }

  /**
   * Confirm the full resolution of current flow
   */
  public static function confirm()
  {
    $node = self::$tree->getNextUnresolved();
    // Are we done ?
    if ($node != null) {
      throw new \feException("You can't confirm an ongoing turn");
    }

    // Callback
    $callback = Globals::getCallbackEngineResolved();
    if (isset($callback['state'])) {
      Game::get()->gamestate->jumpToState($callback['state']);
    } elseif (isset($callback['order'])) {
      Game::get()->nextPlayerCustomOrder($callback['order']);
    } elseif (isset($callback['method'])) {
      $name = $callback['method'];
      Game::get()->$name();
    }
  }

  public static function confirmPartialTurn()
  {
    $node = self::$tree->getNextUnresolved();

    // Are we done ?
    if ($node == null) {
      throw new \feException("You can't partial confirm an ended turn");
    }

    $oldPId = Game::get()->getActivePlayerId();
    $pId = $node->getPId();

    if ($oldPId == $pId) {
      throw new \feException("You can't partial confirm for the same player");
    }

    // Clear log
    self::checkpoint();
    Engine::proceed(true);
  }

  /**
   * Restart the whole flow
   */
  public static function restart()
  {
    Log::undoTurn();

    // Force to clear cached informations
    Globals::fetch();
    self::boot();
    self::proceed(false, true);
  }

  /**
   * Restart at a given step
   */
  public static function undoToStep($stepId)
  {
    Log::undoToStep($stepId);

    // Force to clear cached informations
    Globals::fetch();
    self::boot();
    self::proceed(false, true);
  }

  /**
   * Clear all nodes related to the current active zombie player
   */
  public static function clearZombieNodes($pId)
  {
    self::$tree->clearZombieNodes($pId);
  }

  /**
   * Get all resolved actions of given type
   */
  public static function getResolvedActions($types)
  {
    return self::$tree->getResolvedActions($types);
  }

  public static function getLastResolvedAction($types)
  {
    $actions = self::getResolvedActions($types);
    return empty($actions) ? null : $actions[count($actions) - 1];
  }

  // in the case of a discard event (or other case), we need to force the discarded card in listening mode
  public static function forceListeningNode($sourceId, &$nodes = null)
  {
    if (is_null($nodes)) {
      $nodes = self::$tree;
    }

    if ($nodes->getAction() == ACTIVATE_CARD && ($nodes->getArgs()['cardId'] ?? -1) == $sourceId) {
      $args = $nodes->getArgs();
      $args['event']['cardsToListen'][] = $sourceId;
      $nodes->setInfo('args', $args);
    }

    foreach ($nodes->getChilds() as $node) {
      if ($node->isResolved()) {
        continue;
      }
      self::forceListeningNode($sourceId, $node);
    }

    Engine::save();
  }
}
