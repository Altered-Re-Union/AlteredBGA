<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_SmallStepGiantLeap extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_25_R1',
      'asset' => 'ALT_CORE_B_YZ_25_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Small Step, Giant Leap'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'flavorText' => clienttranslate('The journey of a thousand miles begins with one step.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('Target Expedition moves forward one region.'),
      'costHand' => 6,
      'costReserve' => 7,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(MOVE_EXPEDITION, []),
    ];
  }
}
