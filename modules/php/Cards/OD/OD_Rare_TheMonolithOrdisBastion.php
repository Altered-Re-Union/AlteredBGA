<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_TheMonolithOrdisBastion extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_28_R1',
      'asset' => 'ALT_CORE_B_OR_28_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Monolith, Ordis Bastion'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate('A beacon of knowledge and order, a lighthouse towering high above the world.'),
      'artist' => 'Jean-Baptiste Andrier',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate('When a #non-token# Character joins your Expeditions — It gains 1 boost.'),
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['isCardAddedAnyPlayer:characterOnly', 'isStillSameLocation'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
        'MoveCard' => [
          'conditions' => ['isCardAddedAnyPlayer:characterOnly', 'hasSameOwner', 'isStillSameLocation'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
      ],
    ];
  }
}
