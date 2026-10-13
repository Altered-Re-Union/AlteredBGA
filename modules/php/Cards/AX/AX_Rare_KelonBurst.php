<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_KelonBurst extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_23_R1',
      'asset' => 'ALT_CORE_B_AX_23_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kelon Burst'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate(
        'There\'s an enduring legend in the Suspira quarries: the existence of another type of Kelon.'
      ),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate(
        '<FLEETING>.  Choose one:  • Send to Reserve target Character with Hand Cost {4} or less.  • Discard target Permanent with Hand Cost {4} or less.  #If you control two or more Landmarks, create a <BRASSBUG> Robot token in target Expedition.#'
      ),
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        [
          'optional' => true,
          'type' => NODE_XOR,
          'childs' => [
            FT::ACTION(TARGET, ['maxHandCost' => 4, 'effect' => FT::DISCARD_TO_RESERVE()]),
            FT::ACTION(TARGET, ['maxHandCost' => 4, 'targetType' => [PERMANENT], 'effect' => FT::ACTION(DISCARD, [])]),
          ],
        ],
        FT::ACTION(CHECK_CONDITION, [
          'condition' => 'hasControl:landmark:2',
          'effect' => FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'AX_Common_Brassbug',
            'targetLocation' => STORMS,
          ]),
        ])
      ),
    ];
  }
}
