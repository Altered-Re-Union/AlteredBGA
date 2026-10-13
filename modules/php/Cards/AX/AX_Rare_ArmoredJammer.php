<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_ArmoredJammer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_28_R1',
      'asset' => 'ALT_CORE_B_AX_28_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Armored Jammer'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate('"In a jam? Sorry, mate. Maybe a side-effect of our scrambling..."'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate('{J} $<SABOTAGE>.  #When I leave your Landmark zone — <SABOTAGE>.#'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),

      'effectPassive' => [
        'LeaveLandmark' => [
          'output' => FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        ],
      ],
    ];
  }
}
