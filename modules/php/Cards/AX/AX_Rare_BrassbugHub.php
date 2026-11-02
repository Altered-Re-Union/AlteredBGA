<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_BrassbugHub extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_24_R1',
      'asset' => 'ALT_CORE_B_AX_24_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Brassbug Hub'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate(
        'Few people visit the depths of the Foundry, and even fewer can explain how the Brassbugs came to be.'
      ),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate(
        '{J} I gain three Kelon counters.  At Noon — You may pay {1} and spend one of my Kelon counters to create a <BRASSBUG> Robot token in target Expedition.'
      ),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],

      'effectPlayed' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'gainCounter', 'args' => ['counter' => 3, 'counterName' => clienttranslate('Kelon counter')]],
      ],
      'effectPassive' => [
        'Noon' => [
          'conditions' => ['isMe', 'hasCounterOnCard'],
          'output' => [
            'type' => NODE_SEQ,
            'optional' => true,
            'childs' => [
              FT::ACTION(USE_COUNTER, ['pay' => 1, 'consume' => 1]),
              FT::ACTION(INVOKE_TOKEN, [
                'pId' => 'source',
                'tokenType' => 'AX_Common_Brassbug',
                'targetLocation' => STORMS,
              ]),
            ],
          ],
        ],
      ],
    ];
  }
}
