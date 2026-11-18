<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;
use ALT\Core\Engine;

class Draw extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_DRAW;
  }

  public function getDescription()
  {
    $n = $this->getCtxArg('n') ?? 1;
    $players = $this->getCtxArg('players') ?? ALL;
    $location = $this->getArg('location');

    if ($players == ALL) {
      if ($location == MANA) {
        return [
          'log' => clienttranslate('All players puts the top deck card in their mana one'),
          'args' => [
            'n' => $n,
          ],
        ];
      } else {
        return [
          'log' => clienttranslate('All players draw ${n} card(s)'),
          'args' => [
            'n' => $n,
          ],
        ];
      }
    } elseif ($players == OPPONENT) {
      return [
        'log' => clienttranslate('The opponent draws ${n} card(s)'),
        'args' => [
          'n' => $n,
        ],
      ];
    } elseif ($players == OWNER) {
      return [
        'log' => clienttranslate('The owner of the card draws ${n} card(s)'),
        'args' => [
          'n' => $n,
        ],
      ];
    }
    // The reward is for the player
    else {
      return [
        'log' => clienttranslate('Draw ${n} card(s)'),
        'args' => [
          'n' => $n,
        ],
      ];
    }
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  public function isIrreversible($player = null)
  {
    return true;
  }

   protected function getDrawTargetPlayers()
  {
    $who = $this->getCtxArg('players') ?? ALL;

    if ($who == ME) {
      return [Players::getActive()];
    }
    if ($who == ALL) {
      return Players::getAll();
    }
    if ($who == OWNER) {
      if (is_null($this->getArg('ownerId'))) {
        throw new \BgaVisibleSystemException('No owner of card. Should not happen');
      }
      return [Players::get($this->getArg('ownerId'))];
    }
    if ($who == OPPONENT) {
      return [Players::getNext(Players::getActive())];
    }

    return [Players::getActive()];
  }

  public function isDoable($player)
  {
    // Always doable: an empty deck is a no-op in stDraw, not an impossible action.
    // Returning false forced an auto-pass through ST_DRAW (no possibleactions) and
    // blocked later steps in SEQ flows (e.g. Hooked rare: target then draw).
    return true;
  }

  protected $args = [
    'n' => 1,
    'players' => ALL,
    'location' => HAND,
    'tapped' => false,
    'ownerId' => null,
    'skipListeners' => false,
  ];

  public function stDraw()
  {
    $n = $this->getCtxArg('n') ?? 1;

    $source = $this->ctx->getSource() ?? null;
    $sourceId = $this->ctx->getSourceId() ?? null;
    if (is_null($source) && !is_null($sourceId)) {
      $source = Cards::getSingle($sourceId);
    }

    $players = $this->getDrawTargetPlayers();

    foreach ($players as $player) {
      if (!$player->hasDeckCards()) {
        continue;
      }

      if ($this->getArg('location') == MANA) {
        $cards = $player->draw(
          1,
          null,
          MANA,
          $player->getHero(),
          clienttranslate('${player_name} draws 1 card from its deck and put it in mana (${card_name2}\'s effect)'),
          clienttranslate('You draw ${card_names} from your deck and put it in mana (${card_name2}\'s effect)'),
          $this->getArg('tapped')
        );
      } else {
        $cards = $player->draw($n, null, null, $source);
      }
      // Morning draws set skipListeners: they are already reported by the Morning listeners.
      // Without this, reshuffling mid-draw would fire "opponent draws" reactions twice.
      if (!$this->getArg('skipListeners') && $cards->count() > 0 && $this->getArg('location') != MANA) {
        $this->checkAfterListeners($player, ['draw' => $n, 'location' => $this->getArg('location')]);
      }
    }

    $this->resolveAction(null, true);
  }
}
