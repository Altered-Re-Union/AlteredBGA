<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_Red extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_10_R1',
      'asset' => 'ALT_CORE_B_BR_10_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Red'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Grandma would be proud.'),
      'artist' => 'Jean-Baptiste Andrier',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('$<SEASONED_ME_FS>.  #At Dusk — if I have 3 or more boosts, draw a card.#'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 3,
      'changedStats' => ['costReserve'],
      'seasoned' => true,
      'effectPassive' => [
        'BeforeDusk' => [
          'condition' => 'hasBoost:3',
          'output' => FT::ACTION(DRAW, ['players' => ME]),
        ],
      ],
    ];
  }
}
