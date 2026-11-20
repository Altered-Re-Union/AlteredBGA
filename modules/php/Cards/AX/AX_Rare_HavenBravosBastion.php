<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_HavenBravosBastion extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_30_R2',
      'asset' => 'ALT_CORE_B_BR_30_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Haven, Bravos Bastion'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate('Haven isn\'t where legends are born... it\'s where they live forever.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate('#{J} $<RESUPPLY>.#  #{T} The next Character you play from your Reserve this turn gains 1 boost#'),
      'costHand' => 2,
      'costReserve' => 2,
      // 'effectPassive' => [
      //   'ChooseAssignment' => [
      //     'condition' => 'isCharacterFromReserveNotBlocked',
      //     'output' => FT::GAIN(EFFECT, BOOST),
      //   ],
      // ],
      'effectTap' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextReserveCharacterGains1Boost']),
      'effectPlayed' => FT::ACTION(RESUPPLY, []),
    ];
  }
}
