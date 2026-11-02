<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_OrdisCarrier extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_30_R1',
      'asset' => 'ALT_CORE_B_OR_30_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ordis Carrier'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate('The flow of Ordis Recruits seems to go on forever.'),
      'artist' => 'Taras Susak',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate('At Noon — Create an <ORDIS_RECRUIT> Soldier token in #each of your Expeditions#.'),
      'costHand' => 5,
      'costReserve' => 5,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPassive' => [
        'Noon' => [
          'condition' => 'isMe',
          'output' => FT::SEQ(
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'OD_Common_OrdisRecruit',
              'targetLocation' => [STORM_LEFT],
            ]),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'OD_Common_OrdisRecruit',
              'targetLocation' => [STORM_RIGHT],
              'moreThan1' => true,
            ])
          ),
        ],
      ],
    ];
  }
}
