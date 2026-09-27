<?php

namespace ALT\Actions;

use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Core\Globals;

class EndAfternoon extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_END_AFTERNOON;
  }

  public function getDescription()
  {
    return [
      'log' => clienttranslate('End your Afternoon'),
      'args' => [],
    ];
  }

  // public function isOptional($player)
  // {
  //   return true;
  // }

  public function isDoable($player)
  {
    return Globals::isDayPhase() && !in_array($player->getId(), Globals::getSkippedPlayers());
  }

  public function stEndAfternoon()
  {
    $player = Players::getActive();
    $skipped = Globals::getSkippedPlayers();
    if (empty($skipped)) {
      Globals::setFirstPass($player->getId());
    }
    $skipped[] = $player->getId();
    Globals::setSkippedPlayers($skipped);
    Notifications::pass($player);

    // Insert EndTurn reactions as children of this Pass (not root AFTER_FINISHING),
    // so they resolve before remaining parallel siblings (e.g. unique TARGET after 775).
    $reaction = Cards::getReaction(array_merge(
      [
        'pId' => $player->getId(),
        'type' => 'action',
        'action' => 'EndTurn',
        'method' => 'EndTurn',
      ],
      ['pass']
    ));
    if ($reaction !== null) {
      // When multiple "when you pass" effects trigger simultaneously, let the player choose the order
      // per game rules (similar to ChooseAssignment handling of simultaneous effects).
      $this->insertAsChild([
        'type' => NODE_PARALLEL,
        'noIndependent' => count($reaction) > 1,
        'childs' => $reaction,
      ]);
    }
    
    $this->resolveAction();
  }
}
