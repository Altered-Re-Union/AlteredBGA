<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_SunWukong extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_18_R1',
      'asset' => 'ALT_CORE_B_BR_18_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Sun Wukong'),
      'type' => CHARACTER,
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('{R} I gain 2 boosts$<BB> #and lose <FLEETING_CHAR>#.'),
      'typeline' => clienttranslate('Character - Deity'),
      'flavorText' => clienttranslate('Ever the trickster, always the rebel.'),
      'artist' => 'Kevin Sidharta',

      'forest' => 2,
      'mountain' => 4,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 4,
      'effectReserve' => FT::SEQ(FT::GAIN(ME, BOOST, 2), FT::LOOSE(ME, FLEETING)),
    ];
  }
}
