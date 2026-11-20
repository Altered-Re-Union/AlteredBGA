<?php

namespace ALT\Helpers;

use ALT\Core\Globals;
use ALT\Managers\Players;

trait DecksLockTournamentTrait
{
	const DECK_KEY = 'TournamentDeck';
    const DECK_LOCK_TOURNAMENT_SELECT_DECK = 'selectDeck';
    const DECK_LOCK_TOURNAMENT_PASS_SELECT_DECK = 'passDeckSelection';

    private array $tournamentDecks = [];
    private ?bool $isDecksLockTournament = null;

    public function stDeckLockTournament()
    {
        if ($this->isDecksAlreadyLocked()) {
            $this->deckLockTournament();
            $this->gamestate->nextState(self::DECK_LOCK_TOURNAMENT_PASS_SELECT_DECK);
        } else {
            $this->gamestate->nextState(self::DECK_LOCK_TOURNAMENT_SELECT_DECK);
        }
    }

	public function isDeckLockTournamentEnable(): bool
	{   
        if ($this->isDecksLockTournament  === null)
        {
            $this->isDecksLockTournament = $this->bga->tournament->isTournament() 
                && $this->bga->tableOptions->get(OPTION_DECK_LOCK_TOURNAMENT);
            
        }
        return $this->isDecksLockTournament;
	}

    public function isDecksAlreadyLocked(): bool
    {
        if (!$this->isDeckLockTournamentEnable()) {
            return false;
        }

        if (empty($this->tournamentDecks)) {
            foreach (Players::getAll() as $pId => $player) {
                $deck = $this->getPlayerDeck($pId);
                if (!$deck) {
                    $this->tournamentDecks = [];
                    return false;
                }
                $this->tournamentDecks[$pId] = $deck;
            }
        }

        return true;
    }

	public function deckLockTournament()
	{
		if ($this->isDeckLockTournamentEnable()) {
            if ($this->isDecksAlreadyLocked()) {
                Globals::setDeckContent($this->tournamentDecks);
            } else {
                foreach (Players::getAll() as $pId => $player) {
                    $gContent = Globals::getDeckContent();
                    $this->savePlayerDeck($pId, $gContent[$pId]);
                }
            }
		}
	}

	private function getPlayerDeck(int $playerId): array
	{
		return $this->bga->tournament->retrievePlayerGameData($playerId, self::DECK_KEY);
	}

	private function savePlayerDeck(int $playerId, array $deck)
	{
		
		$this->bga->tournament->storePlayerGameData($playerId, self::DECK_KEY, $deck);
	}
}
