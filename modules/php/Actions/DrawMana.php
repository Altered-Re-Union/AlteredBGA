<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;
use ALT\Core\Engine;

class DrawMana extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_DRAW_MANA;
  }

  public function getDescription()
  {
    return [
      'log' => clienttranslate('Put ${n} to mana'),
      'args' => [
        'n' => $this->getArg('n'),
      ],
    ];
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  public function isIrreversible($player = null)
  {
    return true;
  }
  
  public function isDoable($player)
  {
    return true;
  }

  public function getPlayer()
  {
    $pId = $this->getCtxArg('pId') ?? Players::getActiveId();
    return Players::get($pId);
  }

  protected $args = [
    'n' => 1,
    'location' => MANA,
    'tapped' => true,
  ];

  public function stDrawMana()
  {
    $n = $this->getArg('n');
    $player = $this->getPlayer();

    $source = $this->ctx->getSource() ?? null;
    $sourceId = $this->ctx->getSourceId() ?? null;
    if (is_null($source) && !is_null($sourceId)) {
      $source = Cards::getSingle($sourceId);
    }

    if (!$player->hasDeckCards()) {
      $this->resolveAction(null, true);
      return;
    }

    $cards = Cards::pickForLocation($n, 'deck-' . $player->getId(), $this->getArg('location'));
    foreach ($cards as $cId => &$card) {
      $card->setTapped($this->getArg('tapped'));
    }

    Notifications::discardMana($player, $cards, null, clienttranslate('${player_name} draws ${n} card(s) and put them as mana'), ['fromLocation' => 'deck']);

    $this->resolveAction(null, true);
  }
}
