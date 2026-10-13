<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_Inari extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_11_R1',
      'asset' => 'ALT_CORE_B_MU_11_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Inari'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'Harmony may bloom from a single act of kindness, as a rice field may sprout from a single grain of rice. '
      ),
      'artist' => 'Matteo Spirito',
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('#At Noon — Draw a card.#'),
      'forest' => 3,
      'mountain' => 1,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 2,
      'effectPassive' => [
        'Noon' => [
          'condition' => 'isMe',
          'output' => FT::ACTION(DRAW, ['players' => ME]),
        ],
      ],
    ];
  }
}
