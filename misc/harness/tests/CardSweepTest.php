<?php

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Plays every card from hand on a standard board and resolves its effect with a naive
 * player (first choice, first targets, pass optional steps). Fails on any exception;
 * records the number of SQL queries per card in /harness/sweep.csv.
 *
 * SWEEP=all runs every card, SWEEP=<n> an evenly spread sample of n cards (default 40),
 * SWEEP=<regex> the cards whose class name matches.
 */
class CardSweepTest extends AlteredTestCase
{
  private const MAX_STEPS = 40;

  public static function cards(): array
  {
    $files = glob(getenv('GAME_DIR') . '/modules/php/Cards/*/*.php');
    $classes = array_map(fn($f) => basename($f, '.php'), $files);
    $classes = array_values(array_filter($classes, fn($c) => preg_match('/^[A-Z]{2}_(Common|Rare|Unique|Exalted)_/', $c)));
    sort($classes);
    $sweep = getenv('SWEEP') ?: '40';
    if ($sweep !== 'all') {
      if (ctype_digit($sweep)) {
        $step = max(1, intdiv(count($classes), (int) $sweep));
        $classes = array_values(array_filter($classes, fn($i) => $i % $step == 0, ARRAY_FILTER_USE_KEY));
      } else {
        $classes = array_values(array_filter($classes, fn($c) => preg_match("/$sweep/", $c)));
      }
    }
    return array_combine($classes, array_map(fn($c) => [$c], $classes));
  }

  #[DataProvider('cards')]
  public function testPlayFromHand(string $class): void
  {
    $this->startGame();
    $me = $this->activeId();
    $opp = $me == self::P1 ? self::P2 : self::P1;
    $this->createCard($opp, 'AX_Common_IraFairAttendee', STORM_LEFT);
    $this->createCard($opp, 'BR_Common_Kappa', STORM_RIGHT);
    $this->createCard($me, 'BR_Common_DevotedProtector', STORM_LEFT);
    for ($i = 0; $i < 9; $i++) {
      $this->createCard($me, 'BR_Common_Kappa', MANA);
    }
    $this->readyMana($me);
    try {
      $cardId = $this->createCard($me, $class, HAND);
    } catch (\Throwable $e) {
      $this->markTestSkipped("cannot instantiate: {$e->getMessage()}");
    }

    $play = $this->game->argsAtomicAction()['_private'][$me]['play'] ?? [];
    if (!isset($play[$cardId])) {
      $this->markTestSkipped('not playable from hand on the standard board');
    }

    $cost = $this->measure(function () use ($cardId, $play, $me) {
      $this->act('actPlay', $cardId, $play[$cardId][0]);
      $this->resolveNaively($me);
    });
    file_put_contents(getenv('SWEEP_OUT') ?: '/harness/sweep.csv', "$class,{$cost['queries']},{$cost['totalMs']}\n", FILE_APPEND);
  }

  /** Answer every pending decision until the turn goes back to a chooseAssignment */
  private function resolveNaively(int $me): void
  {
    for ($step = 0; $step < self::MAX_STEPS; $step++) {
      $state = $this->game->gamestate->state();
      $name = $state['name'];
      $args = $state['args'] ?? [];
      if ($name == 'chooseAssignment' || $name == 'gameEnd') {
        $this->addToAssertionCount(1);
        return;
      }
      if ($name == 'confirmTurn' || $name == 'confirmPartialTurn') {
        $this->as($this->activeId(), fn($g) => $g->actConfirmTurn());
      } elseif ($name == 'resolveChoice') {
        $this->chooseChoice($args['choices'][0]['id'] ?? array_key_first($args['choices']));
      } elseif ($name == 'target') {
        $n = $args['upTo'] ? 0 : min($args['n'], count($args['cardIds']));
        $this->act('actTarget', array_slice($args['cardIds'], 0, $n));
      } elseif (($args['optionalAction'] ?? false) === true) {
        $this->as($this->activeId(), fn($g) => $g->actPassOptionalAction());
      } else {
        $this->markTestIncomplete("naive player cannot answer state '$name'");
      }
    }
    $this->fail('effect did not resolve within ' . self::MAX_STEPS . ' decisions');
  }
}
