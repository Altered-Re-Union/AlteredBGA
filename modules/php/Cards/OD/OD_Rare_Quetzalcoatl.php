<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_Quetzalcoatl extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_18_R1',
      'asset' => 'ALT_CORE_B_OR_18_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Quetzalcóatl'),
      'typeline' => clienttranslate('Character - Deity Bureaucrat'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('A soul has yet to be found who is bold enough to contest his wisdom.'),
      'artist' => 'Taras Susak',
      'subtypes' => [DEITY, BUREAUCRAT],
      'effectDesc' => clienttranslate(
        'When an opponent draws one or more cards or does <RESUPPLY_T> — Create an <ORDIS_RECRUIT> Soldier token in target Expedition.'
      ),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['forest', 'mountain', 'ocean', 'costHand', 'costReserve'],
      'effectPassive' => [
        'Draw' => [
          'condition' => 'isOpponentDraw',
          'output' => FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'OD_Common_OrdisRecruit',
          ]),
        ],
        'Resupply' => [
          'conditions' => ['isOpponentDraw', 'realResupply'],
          'output' => FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'OD_Common_OrdisRecruit',
          ]),
        ],
        'Morning' => [
          'condition' => 'isMe',
          'output' => FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'OD_Common_OrdisRecruit',
          ]),
        ],
      ],
    ];
  }
}
