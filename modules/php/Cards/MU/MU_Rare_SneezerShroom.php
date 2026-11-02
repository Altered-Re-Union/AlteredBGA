<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_SneezerShroom extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_08_R1',
      'asset' => 'ALT_CORE_B_MU_08_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Sneezer Shroom'),
      'type' => CHARACTER,
      'subtypes' => [PLANT],
      'effectDesc' => clienttranslate('{J} I gain $<ANCHORED>.  #At Noon — I gain 1 boost$<BB>.#'),
      'typeline' => clienttranslate('Character - Plant'),
      'flavorText' => clienttranslate('"Achoo!"'),
      'artist' => 'Zero Wen',

      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::GAIN(ME, ANCHORED),
      'effectPassive' => ['Noon' => ['condition' => 'isMe', 'output' => FT::GAIN(ME, BOOST)]],
    ];
  }
}
