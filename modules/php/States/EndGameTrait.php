<?php

namespace ALT\States;

use ALT\Core\Globals;
use ALT\Core\Notifications;
use ALT\Core\Engine;
use ALT\Core\Stats;
use ALT\Helpers\Log;
use ALT\Managers\Players;
use ALT\Managers\Meeples;
use ALT\Managers\Cards;
use ALT\Models\Card;

trait EndGameTrait
{
  function stPreEndOfGame($concede = false)
  {
    // TODO: API call
    $request = [];

    $tableId = (string) $this->table_id;
    $tournamentInfo = [];
    $tournamentSeeds = [];
    if ($this->bga->tournament->isTournament()) {
      $tournamentInfo = $this->bga->tournament->getInfo();
      $tournamentSeeds = $this->bga->tournament->getSeedInfo();
    }
    $players = [];
    $winningId = null;
    foreach (Players::getAll() as $pId => $player) {
      $deck = '';
      foreach (Globals::getDeckContent()[$pId]['cards'] ?? [] as $entry) {
        $card = $entry['card'] ?? null;
        $uid = $card instanceof Card ? $card->getUid() : ($card['properties']['uid'] ?? null);
        if ($uid !== null) {
          $deck .= "{$entry['n']} {$uid}\n";
        }
      }
      $played = Globals::getPlayedCardsHistory()[$pId] ?? [];
      $playedStr = '';
      foreach ($played as $uid => $n) {
        $playedStr .= "{$n} {$uid}\n";
      }
      $faction = $player->getFaction();
      $players[] = [
        'id' => $pId,
        'name' => $player->getName(),
        'faction' => $faction == FACTION_OD ? 'OR' : $faction,
        'deck' => $deck,
        'playedCards' => $playedStr,
      ];
      if (Stats::getWinner($player) > 0) {
        $winningId = $pId;
      }
    }

    if (!Globals::getZombie() || Globals::getDay() >= 4) {
      //$valid = self::getGenericGameInfos('push_adventure_pass', $request);

      Notifications::message(
        clienttranslate('The game has ended.'),
        []
      );
      $result = $this->getGenericGameInfos('register_game', [
        'player1Id' => $players[0]['id'],
        'player2Id' => $players[1]['id'],
        'payload' => [
            'format' => Globals::getDeckFormat(),
            'tableId' => $tableId,
            'tournamentId' => $tournamentInfo['id'] ?? null,
            'tournamentName' => $tournamentInfo['name'] ?? null,
            'tournamentSeed' => $tournamentSeeds['tournament_seed'] ?? null,
            'tournamentParentId' => $tournamentInfo['tournament_parent_id'] ?? null,
            'tournamentGroup' => $tournamentInfo['tournament_group'] ?? null,
            'env' => $this->getGameName(),
            'players' => $players,
            'winningId' => $winningId,
        ],
      ]);
      // if ($valid['success'] == 1 && isset($valid['winner_bga_adventure_pass_progress']) && !is_null($valid['winner_bga_adventure_pass_progress'])) {
      //   Notifications::message(
      //     clienttranslate('${player_name} increased the BGA Adventure pass to ${pass}'),
      //     [
      //       'player' => Players::get($request['winner']['id']),
      //       'pass' => $valid['winner_bga_adventure_pass_progress']
      //     ]
      //   );
      // }
      // if ($valid['success'] == 1 && isset($valid['loser_bga_adventure_pass_progress']) && !is_null($valid['loser_bga_adventure_pass_progress'])) {
      //   Notifications::message(
      //     clienttranslate('${player_name} increased the BGA Adventure pass to ${pass}'),
      //     [
      //       'player' => Players::get($request['loser']['id']),
      //       'pass' => $valid['loser_bga_adventure_pass_progress']
      //     ]
      //   );
      // }
    }
    // throw new \feException(print_r($valid));
    // TODO remove in alpha
    // [success] => 1
    // [transaction_id] => 01KG2N443JATCX4XYH55Q30PGT
    // [winner_adventure_pass_point] => 0
    // [winner_bga_adventure_pass_progress] => 3/15
    // [loser_adventure_pass_point] => 
    // [loser_bga_adventure_pass_progress] => 
    if ($this->getBgaEnvironment() == 'studio') {
      Notifications::message(clienttranslate('${request}'), [
            'request' => json_encode($request, JSON_PRETTY_PRINT),
      ]);
    }
    if (!$concede) {
      $this->gamestate->nextState('');
    }
  }

  /*
    function stLaunchEndOfGame()
    {
      foreach (ZooCards::getAllCardsWithMethod('EndOfGame') as $card) {
        $card->onEndOfGame();
      }
      Globals::setTurn(15);
      Globals::setLiveScoring(true);
      Scores::update(true);
      Notifications::seed(Globals::getGameSeed());
      $this->gamestate->jumpToState(\ST_END_GAME);
    }
    */
}
