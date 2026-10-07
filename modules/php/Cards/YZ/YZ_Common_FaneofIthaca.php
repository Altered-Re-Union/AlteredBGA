<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_FaneofIthaca extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_146_C',
      'asset' => 'ALT_FUGUE_B_YZ_146_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Fane of Ithaca'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Jean-Baptiste Andrier',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('{J} I gain 3 Mirage counters.  At Noon — If you have six or more Mana Orbs, you may spend 1 of my Mirage counters to draw a card.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'gainCounter', 'args' => ['counter' => 3, 'counterName' => clienttranslate('Mirage counter')]],
      ],
      'effectPassive' => [
        'Noon' => [
          'conditions' => ['isMe', 'hasCounterOnCard', 'hasXMana:6'],
          'output' => [
            'type' => NODE_SEQ,
            'optional' => true,
            'childs' => [
              FT::ACTION(USE_COUNTER, ['consume' => 1]),
              FT::ACTION(DRAW, ['players' => ME]),
            ],
          ],
        ],
      ],
    ];
  }
}
