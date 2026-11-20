<?php

namespace ALT\Core\Engine;

use ALT\Managers\Actions;
use ALT\Core\Globals;

/*
 * Leaf: a class that represent a Leaf
 */

class LeafNode extends AbstractNode
{
  public function __construct($infos = [])
  {
    parent::__construct($infos, []);
    $this->infos['type'] = NODE_LEAF;
  }

  /**
   * An action leaf is resolved as soon as the action is resolved
   */
  public function isResolved()
  {
    return parent::isResolved() || ($this->getAction() != null && $this->isActionResolved());
  }

  public function isAutomatic($player = null)
  {
    if (!isset($this->infos['action'])) {
      return false;
    }
    return $this->memoized('isAutomatic', $player, function () use ($player) {
      return Actions::get($this->infos['action'], $this)->isAutomatic($player) && Globals::getEngineChoices() <= 20;
    });
  }

  public function isIndependent($player = null)
  {
    if (!isset($this->infos['action'])) {
      return false;
    }
    return $this->memoized('isIndependent', $player, function () use ($player) {
      return Actions::get($this->infos['action'], $this)->isIndependent($player) && Globals::getEngineChoices() <= 20;
    });
  }

  public function isOptional($player)
  {
    return $this->memoized('isOptional', $player, function () use ($player) {
      return $this->computeIsOptional($player);
    });
  }

  private function computeIsOptional($player)
  {
    if ((isset($this->infos['mandatory']) && $this->infos['mandatory']) || Actions::get($this->infos['action'], $this)->isMandatory()) {
      return false;
    }

    if (!is_null($this->getPId()) && $this->getPId() != $player->getId()) {
      return false;
    }

    if (parent::isOptional($player) || !isset($this->infos['action'])) {
      return parent::isOptional($player);
    }

    return Actions::get($this->infos['action'], $this)->isOptional($player) ||
      !Actions::get($this->infos['action'], $this)->isDoable($player);
  }

  public function isIrreversible($player = null)
  {
    if (!isset($this->infos['action'])) {
      return false;
    }
    return Actions::get($this->infos['action'], $this)->isIrreversible($player);
  }

  /**
   * A Leaf is doable if the corresponding action is doable by the player
   */
  public function isDoable($player)
  {
    return $this->memoized('isDoable', $player, function () use ($player) {
      return $this->computeIsDoable($player);
    });
  }

  private function computeIsDoable($player)
  {
    // Useful for a SEQ node where the 2nd node might become doable thanks to the first one
    if (isset($this->infos['willBeDoable'])) {
      return true;
    }
    // Edge case when searching undoable mandatory node pending
    if ($this->isResolved()) {
      return true;
    }
    if (isset($this->infos['action'])) {
      return $player->canTakeAction($this->infos['action'], $this);
    }

    var_dump(is_null($this->parent) ? $this->toArray() : $this->parent->toArray());
    throw new \Bga\GameFramework\VisibleSystemException('Unimplemented isDoable function for non-action Leaf');
  }

  /**
   * The state is either hardcoded into the leaf, or correspond to the attached action
   */
  public function getState()
  {
    if (isset($this->infos['state'])) {
      return $this->infos['state'];
    }

    if (isset($this->infos['action'])) {
      return Actions::getState($this->infos['action'], $this);
    }
    // throw new \feException(print_r($this->infos));
    // throw new \feException(print_r(\ALT\Core\Engine::$tree->toArray()));
    throw new \Bga\GameFramework\VisibleSystemException('Trying to get state on a leaf without state nor action');
  }

  /**
   * The description is given by the corresponding action
   */
  public function getDescription()
  {
    if (isset($this->infos['action'])) {
      return Actions::get($this->infos['action'], $this)->getDescription();
    }
    return parent::getDescription();
  }

  // public function getSourceId()
  // {
  //   throw new \feException($this->infos['sourceId']);
  // }
}
