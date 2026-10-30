<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_TiedtotheMast extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_145_C',
      'asset' => 'ALT_FUGUE_B_MU_145_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Tied to the Mast'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  Target Character with Base Cost {2} or less gains <ANCHORED>. If it\'s a <COMPANION>, it gains 1 boost.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 2,
          'effect' => FT::SEQ(
            FT::GAIN(EFFECT, ANCHORED),
            FT::ACTION(CHECK_CONDITION, [
              'condition' => 'isTargetSubtype:' . COMPANION,
              'effect' => FT::GAIN(EFFECT, BOOST, 1),
            ]),
          ),
        ]),
      ),
    ];
  }
}
