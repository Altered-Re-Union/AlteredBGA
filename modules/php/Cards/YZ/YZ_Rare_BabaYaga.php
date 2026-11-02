<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_BabaYaga extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_11_R1',
      'asset' => 'ALT_CORE_B_YZ_11_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Baba Yaga'),
      'typeline' => clienttranslate('Character - Mage'),
      'type' => CHARACTER,
      'subtypes' => [MAGE],
      'effectDesc' => clienttranslate('{H} Draw a card.'),
      'flavorText' => clienttranslate('Help or harm? Only the card will tell.'),
      'artist' => 'Atanas Lozanski',

      'forest' => 3,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['forest', 'mountain'],
      'effectHand' => FT::ACTION(DRAW, ['players' => ME]),
    ];
  }
}
