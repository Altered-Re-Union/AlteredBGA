<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_KadigiranMageDancer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_07_R1',
      'asset' => 'ALT_CORE_B_YZ_07_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kadigiran Mage-Dancer'),
      'typeline' => clienttranslate('Character - Soldier Mage'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Don\'t just wait for magic to happen. Go out and make your own!"'),
      'artist' => 'Nestor Papatriantafyllou',
      'subtypes' => [SOLDIER, MAGE],
      'effectDesc' => clienttranslate(
        'When you play a Spell — I gain 1 boost.  #At Dusk —  if I have 3 or more boosts, draw a card.#'
      ),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'effectPassive' => [
        'ChooseAssignment' => [
          'condition' => 'isCardPlayed:spell',
          'output' => FT::GAIN(ME, BOOST),
        ],
        'AtDusk' => [
          'condition' => 'hasBoost:3',
          'output' => FT::ACTION(DRAW, ['players' => ME]),
        ],
      ],
    ];
  }
}
