<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_TimeFlies extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_147_C',
      'asset' => 'ALT_FUGUE_B_MU_147_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Time Flies'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  Draw a card.  Then, target opponent becomes first player. (They take the First Player marker, and will pass it at the start of the next Day.)'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(DRAW, ['players' => ME]),
        FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'switchPlayer'])
        ])
      ),
    ];
  }
}
