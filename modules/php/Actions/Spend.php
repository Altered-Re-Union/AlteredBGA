<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Notifications;
use ALT\Core\Stats;
use ALT\Helpers\Utils;
use ALT\Core\Globals;
use ALT\Helpers\Log;
use ALT\Core\Engine;
use ALT\Helpers\FT;
use ALT\Helpers\Collection;

class Spend extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_SPEND;
  }

  public function getDescription()
  {
    $n = $this->getCtxArg('n') ?? 1;

    if (empty($this->getCtxArg('effect'))) {
      return [
        'log' => clienttranslate('Spend ${n}'),
        'args' => ['n' => $n],
      ];
    }

    return [
      'log' => clienttranslate('Spend ${n} to ${effect_desc}'),
      'args' => [
        'n' => $n,
        'effect_desc' => Engine::buildTree($this->getCtxArg('effect'))->getDescription(),
        'i18n' => ['effect_desc'],

      ],
    ];
  }

  public function isAutomatic($player = null)
  {
    return true;
  }


  protected $args = [
    'n' => 1,
    'effect' => [],
    'automatic' => true,
    'updateN' => false,
  ];

  public function getSource()
  {
    $source = $this->ctx->getSource() ?? null;
    $sourceId = $this->ctx->getSourceId() ?? null;
    if (is_null($source) && !is_null($sourceId)) {
      $source = Cards::getSingle($sourceId);
    }
    return $source;
  }

  public function getCard()
  {
    $cardId = $this->resolveSpendCardId();

    if (is_null($cardId)) {
      throw new \BgaVisibleSystemException('no card in args (Spend). Should not happen');
    }
    return Cards::getSingle($cardId);
  }

  /**
   * Card to spend boosts/counters from: explicit args, event (EFFECT), or parent Target pick.
   */
  private function resolveSpendCardId()
  {
    $cardId = $this->getCtxArg('cardId');
    if ($cardId == ME) {
      $cardId = $this->ctx->getSourceId() ?? null;
    } elseif ($cardId == EFFECT) {
      $event = $this->getEventRecursive();
      if (!is_null($event)) {
        $cardId = $event['cardId'] ?? null;
        if (is_null($cardId)) {
          $cardId = $event['gain']['cardId'] ?? null;
        }
      }
    }

    if (!is_null($cardId)) {
      return $cardId;
    }

    $ctx = $this->ctx->getParent();
    while (!is_null($ctx)) {
      if ($ctx->getAction() === \TARGET && $ctx->isActionResolved()) {
        $resolved = $ctx->getActionResolutionArgs();
        if (is_array($resolved) && !empty($resolved)) {
          $pick = $resolved[0];
          $cardId = is_array($pick) ? ($pick[0] ?? null) : $pick;
          if (!is_null($cardId)) {
            return $cardId;
          }
        }
      }
      $ctx = $ctx->getParent();
    }

    return null;
  }


  public function argsSpend()
  {
    return [
      'source' => $this->getSource(),
      'n' => $this->getArg('automatic') == false  ? $this->getCard()->countCounters() : $this->getArg('n'),
      'descSuffix' => $this->getArg('automatic') == false ? 'choice' : '',
      'effect_desc' => empty($this->getCtxArg('effect'))
        ? ''
        : Engine::buildTree($this->getCtxArg('effect'))->getDescription(),
    ];
  }

  /**
   * Whether $card has enough spendable resources for amount $n.
   * Boost tokens are checked first; numeric counters are used only when there are no boosts.
   */
  public static function hasEnoughToSpend($card, $n)
  {
    if ($card->countToken(BOOST) > 0) {
      return $card->countToken(BOOST) >= $n;
    }
    return ($card->getExtraDatas()['counter'] ?? 0) >= $n;
  }

  public function isDoable($player)
  {
    $card = $this->getCard();
    return self::hasEnoughToSpend($card, $this->getArg('n'));
  }

  public function stSpend()
  {
    if ($this->getArg('automatic') === true) {
      $this->actSpend($this->getArg('n'));
    }
  }

  public function actSpend($n)
  {
    $player = Players::getActive();
    $source = $this->getSource();
    $card = $this->getCard();
    $amount = $n;
    // Boost tokens take priority over numeric counters when both are present.
    if ($card->countToken(BOOST) > 0) {
      $deleted = new Collection();
      $meeples = $card->getOfType(BOOST);

      foreach ($meeples as $mId => $m) {
        if ($amount == 0) {
          break;
        }
        $deleted[$mId] = $m;
        $amount--;
      }
      if (!empty($deleted)) {
        Meeples::delete($deleted->getIds());

        if (count($deleted) > 0) {
          Notifications::spend(BOOST, $card, $deleted, false);
        }
      }
    } else {
      // we consume a counter
      $extraDatas = $card->getExtraDatas();
      if (!isset($extraDatas['counter']) || $extraDatas['counter'] == 0) {
        throw new \BgaUserException(clienttranslate('No counter to spend'));
      }

      if (($extraDatas['counter'] ?? 0) < $amount) {
        throw new \BgaVisibleSystemException('Cannot consume counter. Should not happen');
      }

      $extraDatas['counter'] -= $amount;
      $card->setExtraDatas($extraDatas);

      Notifications::spendCounter($player, $card, $amount, $this->getSource());
    }

    $effect = $this->getArg('effect');
    if (!empty($effect)) {
      // Keep GAIN(EFFECT)/etc. referring to the trigger event card, not the card boosts were spent from.
      $effect = $this->updateCardId($effect, $card->getId(), $card->getLocation(), $this->getSourceId(), $card->getPlayer()->getId(), true);
      if ($this->getArg('updateN') == true) {
        $effect['args']['n'] = $amount;
      }
      // throw new \feException(print_r($effect));
      $this->insertAsChild($effect);
    }
    $this->resolveAction();
  }
}
