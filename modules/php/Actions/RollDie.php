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
use ALT\Core\Game;
use ALT\Helpers\FT;
use ALT\Core\Engine;

class RollDie extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_ROLL_DIE;
  }

  public function getDescription()
  {
    list($nTotal, $extraRolls) = $this->getN();

    return [
      'log' => clienttranslate('Roll ${n} die'),
      'args' => [
        'n' => $nTotal,
      ],
    ];
  }

  public function getN($player = null)
  {
    $n = $this->getCtxArg('n') ?? 1;

    $player = $player ?? $this->getPlayer() ?? Players::getActive();

    // Lyra Bastion management
    $extraRolls = $player->getAddDice();

    return [$n + $extraRolls, $extraRolls];
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  public function isIrreversible($player = null)
  {
    return true;
  }

  protected $args = [
    'n' => 1,
    'effect' => [],
    'canDiscard' => false,
    'allowCounterIncrease' => false,
    'hasRolled' => false,
  ];

  public function getSource()
  {
    $source = $this->ctx->getSource() ?? null;
    $sourceId = $this->resolveSourceId();
    if (is_null($source) && !is_null($sourceId)) {
      $source = Cards::getSingle($sourceId);
    }
    return $source;
  }

  private function getGain($roll)
  {
    $effect = null;
    foreach ($this->getArg('effect') as $qty => $gain) {
      $lower = 0;
      $upper = null;

      // Quantity of the form : X-Y
      if (\stripos($qty, '-') !== false) {
        $t = \explode('-', $qty);
        $lower = (int) $t[0];
        $upper = (int) $t[1];
      }
      // Quantity of the form : X+
      elseif (\stripos($qty, '+') !== false) {
        $t = \explode('+', $qty);
        $lower = (int) $t[0];
      }
      // Quantity is just an int
      else {
        $lower = (int) $qty;
        $upper = (int) $qty;
      }

      // Check $n against $lower and $upper
      if ($roll >= $lower && ($upper === null || $roll <= $upper)) {
        if ($effect != null) {
          throw new \feException("Duplicate effect found for roll : $roll");
        }
        $effect = $gain;
      }
    }
    if ($effect == 'OPPOSITE') {
      return null;
    }
    return $effect;
  }

  private function getSourceCounterValue()
  {
    if (!$this->getArg('allowCounterIncrease')) {
      return 0;
    }

    $source = $this->getSource();
    if (is_null($source)) {
      return 0;
    }

    return (int) ($source->getExtraDatas()['counter'] ?? 0);
  }

  private function getSelectableRolls($rolls, $counterValue)
  {
    $selectableRolls = [];
    foreach ($rolls as $roll) {
      $selectableRolls[] = $roll;
      for ($i = 1; $i <= $counterValue; $i++) {
        $selectableRolls[] = $roll + $i;
      }
    }
    sort($selectableRolls, SORT_NUMERIC);
    return array_values(array_unique($selectableRolls, SORT_NUMERIC));
  }

  private function getCounterSpendForRoll($selectedRoll, $rawRolls, $counterValue)
  {
    if ($counterValue <= 0 || !$this->getArg('allowCounterIncrease')) {
      return 0;
    }

    $minSpend = null;
    foreach ($rawRolls as $roll) {
      if ($selectedRoll < $roll) {
        continue;
      }
      $needed = $selectedRoll - $roll;
      if ($needed <= $counterValue && (is_null($minSpend) || $needed < $minSpend)) {
        $minSpend = $needed;
      }
    }

    if (is_null($minSpend)) {
      throw new \BgaVisibleSystemException('Selected die value cannot be reached with available Luck counters.');
    }

    return $minSpend;
  }

  public function stPreRollDie()
  {
    $player = $this->getPlayer() ?? Players::getActive();
    list($nTotal, $extraRolls) = $this->getN($player);
    $rolls = [];

    $source = $this->getSource();
    if ($this->getArg('hasRolled') || $this->getCtx()->getFlag() == PRE_ACTION_DONE) {
      return;
    }

    if ($extraRolls > 0) {
      Notifications::message(clienttranslate('${n} dice are added to the roll (Ouroboros Lyra Bastion\'s effect)'), [
        'n' => $extraRolls,
      ]);
    }
    // $fake = 5;
    for ($i = 0; $i < $nTotal; $i++) {
      $roll = bga_rand(1, 6);
      if (Game::get()->getBgaEnvironment() == 'studio') {
        $roll = 6;
      }
      $rolls[] = $roll;
    }

    // Martengale / Fragrant Meerkat: offer +1 (per card) after seeing the result
    $newRolls = [];
    $addRoll = $player->getAddRoll();
    foreach ($rolls as $roll) {
      $newRolls[] = $roll;
      for ($i = 1; $i <= $addRoll; $i++) {
        $newRolls[] = $roll + $i;
      }
    }

    Notifications::roll($player, $rolls, $source);
    Globals::setDiceRolls($newRolls);
    Log::checkpoint(true);
    $this->checkImmediateListeners($player,  ['rolls' => Globals::getDiceRolls(), 'sourceId' => $source->getId()]);
    return true;
  }

  public function argsRollDie()
  {
    $player = $this->getPlayer() ?? Players::getActive();
    $source = $this->getSource();
    $sourceCounterValue = $this->getSourceCounterValue();
    $rawRolls = Globals::getDiceRolls();
    $rolls = $this->getSelectableRolls($rawRolls, $sourceCounterValue);
    $canDiscard =
      $this->getArg('canDiscard') &&
      $player
      ->getReserveCards()
      ->count() > 0;

    return [
      'rolls' => $rolls,
      'rawRolls' => $rawRolls,
      'canDiscard' => $canDiscard,
      'source' => is_null($source) ? '' : $source,
      'counterValue' => $sourceCounterValue,
      'allowCounterIncrease' => $this->getArg('allowCounterIncrease'),
      'cardIds' => $player
        ->getReserveCards()
        ->getIds(),
      'descSuffix' => $canDiscard ? 'bastion' : '',
    ];
  }

  public function stRollDie()
  {
    $args = $this->argsRollDie();
    // throw new \feException(print_r(debug_print_backtrace()));

    if (!$args['canDiscard'] && count($args['rolls']) == 1) {
      // $this->actRollDie($args['rolls'][0]);
      return [$args['rolls'][0]];
    }
  }

  public function actRollDie($dieValue)
  {
    $dieValue = (int) $dieValue;
    $player = $this->getPlayer() ?? Players::getActive();
    $source = $this->getSource();
    $args = $this->argsRollDie();
    $effects = [];

    if (!in_array($dieValue, $args['rolls'], true)) {
      throw new \BgaVisibleSystemException('You cannot select this die value. Should not happen');
    }

    $counterSpend = $this->getCounterSpendForRoll($dieValue, $args['rawRolls'], $args['counterValue']);

    if ($counterSpend > 0) {
      $data = $source->getExtraDatas();
      $data['counter'] = max(0, ($data['counter'] ?? 0) - $counterSpend);
      $source->setExtraDatas($data);
      Notifications::spendCounter($player, $source, $counterSpend, $source);
    }

    if (count($args['rolls']) > 1) {
      Notifications::message(clienttranslate('Die ${dieValue} is selected'), ['dieValue' => $dieValue]);
    }

    $effect = $this->getGain($dieValue);
    if ($effect !== null) {
      $effect = Utils::updateTree($effect, 'die', $dieValue);
      $sourceId = $source->getId();
      $effect['sourceId'] = $sourceId;
      $effect = Utils::tagTree($effect, [
        'sourceId' => $sourceId,
        'event' => [
          'cardId' => $sourceId,
          'sourceId' => $sourceId,
          'pId' => $player->getId(),
          'selectedRoll' => $dieValue,
        ],
      ]);
      $cardId = $this->getCtxArg('cardId') ?? null;
      if (!is_null($cardId) && isset($effect['args']['cardId']) && $effect['args']['cardId'] != ME) {
        $effect['args']['cardId'] = $cardId;
      }
      // Snowball commando's effect
      if (isset($effect['pId']) && $effect['pId'] == 'nextPlayer') {
        $effect['pId'] = Players::getNext($player)->getId();
      }
      $effects[] = $effect;
    }

    if (count($effects) > 1) {
      // TODO: see if we will need seq/or/etc.
      $this->insertAsChild(['type' => NODE_XOR, 'childs' => $effects]);
    } elseif (count($effects) == 1) {
      $this->insertAsChild($effects[0]);
    }

    $listenerArgs = [
      'rolls' => Globals::getDiceRolls(),
      'selectedRoll' => $dieValue,
      'sourceId' => $source->getId(),
    ];
    // Deferred RollDie passives run after this call; flag the first roll so
    // hasNotRolledDieThisDay still passes when those reactions execute.
    if (!$player->hasRolledDieThisDay()) {
      $listenerArgs['firstRollThisDay'] = true;
    }
    $this->checkAfterListeners($player, $listenerArgs);
    $player->markDieRolledThisDay();

    Globals::setDiceRolls([]);
    $this->resolveAction([$dieValue]);
  }

  public function actDiscardAdd($cardId)
  {
    $player = $this->getPlayer() ?? Players::getActive();
    $source = $this->getSource();
    $args = $this->argsRollDie();

    if (count(array_diff($cardId, $args['cardIds'])) != 0) {
      throw new \BgaVisibleSystemException('You cannot target this card. should not happen');
    }
    $this->duplicateAction(['canDiscard' => false, 'hasRolled' => true]);
    $this->insertAsChild(FT::ACTION(DISCARD, ['cardId' => $cardId]));

    $rolls = Globals::getDiceRolls();
    foreach ($rolls as $roll) {
      $newRolls[] = $roll + 2;
    }
    Globals::setDiceRolls($newRolls);
    Notifications::message(clienttranslate('${player_name} discards ${card_name} to add 2 to its rolls.'), [
      'player' => $player,
      'card' => Cards::get($cardId),
    ]);
  }
}
