<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_FaneofIthaca extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_146_R1',
      'asset' => 'ALT_FUGUE_B_YZ_146_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fane of Ithaca'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Jean-Baptiste Andrier',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('{J} I gain #2# Mirage counters.  At Noon — If you have #five or more# Mana Orbs, you may spend 1 of my Mirage counters to draw a card.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'gainCounter', 'args' => ['counter' => 2, 'counterName' => clienttranslate('Mirage counter')]],
      ],
      'effectPassive' => [
        'Noon' => [
          'conditions' => ['isMe', 'hasCounterOnCard', 'hasXMana:5'],
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
