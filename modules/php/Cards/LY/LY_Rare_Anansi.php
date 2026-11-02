<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_Anansi extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_13_R1',
      'asset' => 'ALT_CORE_B_LY_13_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Anansi'),
      'typeline' => clienttranslate('Character - Deity Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'In the end, he had accumulated pretty well all the wisdom that was available. He put it in a gourd and made a stopper for it.'
      ),
      'artist' => 'Taras Susak',
      'subtypes' => [DEITY, ARTIST],
      'effectDesc' => clienttranslate('{J} I gain 1 boost for each card #in each player\'s Reserve#.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['forest', 'mountain', 'ocean'],
      'effectPlayed' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXReserveAll']),
    ];
  }
}
