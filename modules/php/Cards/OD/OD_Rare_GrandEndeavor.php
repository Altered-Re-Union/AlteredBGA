<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_GrandEndeavor extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_29_R1',
      'asset' => 'ALT_CORE_B_OR_29_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Grand Endeavor'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate('The Ordis built a relay of lighthouses to light the way through the Tumult.'),
      'artist' => 'Jean-Baptiste Andrier',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate('#$<TOUGH_2>.#  At Noon — Target Expedition moves forward one region.'),
      'costHand' => 6,
      'costReserve' => 6,
      'effectPassive' => [
        'Noon' => [
          'condition' => 'isMe',
          'output' => FT::ACTION(MOVE_EXPEDITION, []),
        ],
      ],
      'tough' => 2,
    ];
  }
}
