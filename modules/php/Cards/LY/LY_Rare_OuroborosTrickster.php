<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_OuroborosTrickster extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_06_R1',
      'asset' => 'ALT_CORE_B_LY_06_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ouroboros Trickster'),
      'type' => CHARACTER,
      'subtypes' => [CITIZEN],
      'effectDesc' => clienttranslate('{J} Roll a die. On a 4+, I gain #3 boosts#. On a 1-3, I gain 1 boost$<BB>.'),
      'typeline' => clienttranslate('Character - Citizen'),
      'flavorText' => clienttranslate('When luck joins in the game, cleverness scores double.'),
      'artist' => 'Zero Wen',

      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => ['1-3' => FT::GAIN(ME, BOOST, 1), '4+' => FT::GAIN(ME, BOOST, 3)],
      ]),
    ];
  }
}
