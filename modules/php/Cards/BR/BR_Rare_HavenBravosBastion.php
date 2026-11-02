<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_HavenBravosBastion extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_30_R1',
      'asset' => 'ALT_CORE_B_BR_30_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Haven, Bravos Bastion'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate('Haven isn\'t where legends are born... it\'s where they live forever.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate('#{J} $<RESUPPLY>.#  Your Characters have: \" {R} I gain 1 boost.\"'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPassive' => [
        'ChooseAssignment' => [
          'condition' => 'isCharacterFromReserveNotBlocked',
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
      ],
      'effectPlayed' => FT::ACTION(RESUPPLY, []),
    ];
  }
}
