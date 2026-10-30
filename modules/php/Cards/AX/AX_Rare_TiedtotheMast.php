<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_TiedtotheMast extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_145_R2',
      'asset' => 'ALT_FUGUE_B_MU_145_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Tied to the Mast'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  Target Character with Base Cost #{1} or less# gains <ANCHORED>.'),
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 1,
          'effect' => FT::GAIN(EFFECT, ANCHORED),
        ]),
      ),
    ];
  }
}
