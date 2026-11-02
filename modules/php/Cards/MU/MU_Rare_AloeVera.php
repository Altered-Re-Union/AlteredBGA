<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_AloeVera extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_16_R1',
      'asset' => 'ALT_CORE_B_MU_16_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Aloe Vera'),
      'typeline' => clienttranslate('Character - Plant'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Moisturizing every day is essential.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [PLANT],
      'effectDesc' => clienttranslate('#{J} You may pay {1} to have me gain $<ANCHORED>.#  At Noon — $<RESUPPLY>.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 4,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPassive' => [
        'Noon' => [
          'condition' => 'isMe',
          'output' => FT::ACTION(RESUPPLY, []),
        ],
      ],
      'effectPlayed' => FT::SEQ_OPTIONAL(FT::ACTION(PAY, ['pay' => 1]), FT::GAIN(ME, ANCHORED)),
    ];
  }
}
