<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_TimeFlies extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_147_R1',
      'asset' => 'ALT_FUGUE_B_MU_147_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Time Flies'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  <RESUPPLY>.  Then, target opponent becomes first player.'),
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(RESUPPLY, []),
        FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => true,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'switchPlayer'])
        ])
      ),
    ];
  }
}
