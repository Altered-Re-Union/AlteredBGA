<?php

class SmokeTest extends AlteredTestCase
{
  public function testGameCreationReachesDeckSelection(): void
  {
    $this->assertSame('selectPrecoDeck', $this->stateName());
    $this->assertEqualsCanonicalizing([self::P1, self::P2], $this->game->gamestate->getActivePlayerList());
  }

  public function testStartGameReachesFirstAssignment(): void
  {
    $this->startGame();
    $this->assertSame('chooseAssignment', $this->stateName(), print_r($this->game->gamestate->history, true));
    $args = $this->args();
    $this->assertNotEmpty($args['_private'][$this->activeId()]['play'] ?? null, print_r(array_keys($args), true));
  }
}
