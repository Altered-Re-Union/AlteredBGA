<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_ConiferalConeman extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_20_R1',
      'asset' => 'ALT_CORE_B_MU_20_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Coniferal Coneman'),
      'type' => CHARACTER,
      'subtypes' => [PLANT],
      'effectDesc' => clienttranslate('{J} I gain $<ANCHORED>.'),
      'typeline' => clienttranslate('Character - Plant'),
      'flavorText' => clienttranslate('"Dosukoi! Let’s put it in the ground!"'),
      'artist' => 'Damian Audino',

      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::GAIN(ME, ANCHORED),
    ];
  }
}
