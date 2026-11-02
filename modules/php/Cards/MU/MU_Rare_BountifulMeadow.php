<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_BountifulMeadow extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_24_R1',
      'asset' => 'ALT_CORE_B_MU_24_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Bountiful Meadow'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate('There\'s no greater joy than seeing all that is green thrive and grow.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate(
        '#{J} You may pay {1} to $<RESUPPLY_INF>.#  {T} : The next Plant you play this turn costs {1} less.'
      ),
      'costHand' => 2,
      'costReserve' => 2,
      'effectTap' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => PLANT, 'reduction' => 1]],
      ],
      'effectPlayed' => FT::SEQ_OPTIONAL(FT::ACTION(PAY, ['pay' => 1]), FT::ACTION(RESUPPLY, [])),
    ];
  }
}
