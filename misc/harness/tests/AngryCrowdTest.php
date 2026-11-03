<?php

/**
 * Angry Crowd (OD/YZ Rare, OD Common): discard target Character or Permanent,
 * Rare creates a Manaseed only if the target's Base Cost was 2 or less.
 */
class AngryCrowdTest extends AlteredTestCase
{
  private int $me;
  private int $opp;
  private int $cheap;
  private int $expensive;

  private function setUpBoard(string $crowdClass): int
  {
    $this->startGame();
    $this->me = $this->activeId();
    $this->opp = $this->me == self::P1 ? self::P2 : self::P1;
    $crowd = $this->createCard($this->me, $crowdClass, HAND);
    $this->cheap = $this->createCard($this->opp, 'AX_Common_IraFairAttendee', STORM_LEFT); // hand cost 1
    $this->expensive = $this->createCard($this->opp, 'BR_Common_Kappa', STORM_RIGHT); // hand cost 4
    for ($i = 0; $i < 3; $i++) {
      $this->createCard($this->me, 'BR_Common_Kappa', MANA);
    }
    $this->readyMana($this->me);
    return $crowd;
  }

  private function play(int $cardId): void
  {
    $play = $this->game->argsAtomicAction()['_private'][$this->me]['play'];
    $this->assertArrayHasKey($cardId, $play, 'Angry Crowd should be playable');
    $this->act('actPlay', $cardId, $play[$cardId][0]);
  }

  private function manaseeds(): array
  {
    return $this->cardsOf($this->me, LANDMARK, 'NE_Common_Manaseed');
  }

  public function testRareOnCheapTargetDiscardsItAndCreatesManaseed(): void
  {
    $crowd = $this->setUpBoard('OD_Rare_AngryCrowd');
    $this->play($crowd);
    $this->assertSame('resolveChoice', $this->stateName());
    $this->chooseChoice(0); // "max base cost 2" branch: only one target => automatic

    $this->assertSame('discard', $this->location($this->cheap));
    $this->assertNotSame('discard', $this->location($this->expensive));
    $this->assertCount(1, $this->manaseeds());
    $this->assertSame($this->opp, $this->activeId(), 'turn passes to the opponent');
  }

  public function testRareOnExpensiveTargetDiscardsItWithoutManaseed(): void
  {
    $crowd = $this->setUpBoard('YZ_Rare_AngryCrowd');
    $this->play($crowd);
    $this->chooseChoice(1); // "min base cost 3" branch

    $this->assertSame('discard', $this->location($this->expensive));
    $this->assertNotSame('discard', $this->location($this->cheap));
    $this->assertCount(0, $this->manaseeds());
  }

  public function testCommonLetsPlayerPickAnyTargetAndCreatesManaseed(): void
  {
    $crowd = $this->setUpBoard('OD_Common_AngryCrowd');
    $this->createCard($this->me, 'BR_Common_Kappa', MANA); // costs 5
    $this->readyMana($this->me);
    $this->play($crowd);
    $this->assertSame('target', $this->stateName());
    $this->assertEqualsCanonicalizing(
      [$this->cheap, $this->expensive],
      array_intersect($this->args()['cardIds'], [$this->cheap, $this->expensive])
    );
    $this->act('actTarget', [$this->expensive]);

    $this->assertSame('discard', $this->location($this->expensive));
    $this->assertCount(1, $this->manaseeds());
  }

  /** Not a regression test: prints the cost of playing the Rare, to compare before/after a fix */
  public function testReportRareCost(): void
  {
    $crowd = $this->setUpBoard('OD_Rare_AngryCrowd');
    $playCost = $this->measure(fn() => $this->play($crowd), true);
    $chooseCost = $this->measure(fn() => $this->chooseChoice(0), true);
    $this->log('play:   ' . json_encode(array_diff_key($playCost, ['top' => 1])));
    $this->log('choose: ' . json_encode(array_diff_key($chooseCost, ['top' => 1])));
    $this->log('top queries during play: ' . print_r($playCost['top'], true));
    $this->assertCount(1, $this->manaseeds());
  }

  public function testReportBaselineCosts(): void
  {
    $this->setUpBoard('OD_Rare_AngryCrowd');
    $plain = $this->createCard($this->me, 'AX_Common_IraFairAttendee', HAND);
    $common = $this->createCard($this->me, 'OD_Common_AngryCrowd', HAND);
    $args = $this->measure(fn() => $this->game->argsAtomicAction());
    $this->log('args chooseAssignment: ' . json_encode($args));
    $this->log('play plain character:  ' . json_encode($this->measure(fn() => $this->play($plain))));
    $this->assertTrue(true);
  }

  public function testReportCommonCost(): void
  {
    $crowd = $this->setUpBoard('OD_Common_AngryCrowd');
    $this->createCard($this->me, 'BR_Common_Kappa', MANA);
    $this->readyMana($this->me);
    $this->log('play common crowd:     ' . json_encode($this->measure(fn() => $this->play($crowd))));
    $this->log('target common crowd:   ' . json_encode($this->measure(fn() => $this->act('actTarget', [$this->expensive]))));
    $this->assertTrue(true);
  }
}
