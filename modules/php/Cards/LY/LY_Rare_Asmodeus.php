<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_Asmodeus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_20_R1',
      'asset' => 'ALT_CORE_B_LY_20_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Asmodeus'),
      'type' => CHARACTER,
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('{J} Roll a die. On a 4+, I gain $<ANCHORED>. On a 1-3, I gain 3 boosts$<BB>.'),
      'typeline' => clienttranslate('Character - Deity'),
      'flavorText' => clienttranslate('Wanna play a game?'),
      'artist' => 'Zero Wen',

      'forest' => 4,
      'mountain' => 4,
      'ocean' => 4,
      'costHand' => 5,
      'costReserve' => 5,
      'changedStats' => ['forest', 'mountain', 'ocean', 'costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => ['1-3' => FT::GAIN(ME, BOOST, 3), '4+' => FT::GAIN(ME, ANCHORED)],
      ]),
    ];
  }
}
