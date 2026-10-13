<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_YongSuVerdantWeaver extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_10_R1',
      'asset' => 'ALT_CORE_B_MU_10_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Yong-Su, Verdant Weaver'),
      'typeline' => clienttranslate('Character - Druid'),
      'type' => CHARACTER,
      'subtypes' => [DRUID],
      'effectDesc' => clienttranslate(
        '{J} If you control two or more Plants, I gain 2 boosts$<BB>. (Cards in Reserve are not controlled.)'
      ),
      'flavorText' => clienttranslate('Green is good.'),
      'artist' => 'Kevin Sidharta',

      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['forest', 'mountain', 'ocean', 'costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(CHECK_CONDITION, ['condition' => 'hasControl:plant:2', 'effect' => FT::GAIN(ME, BOOST, 2)]),
    ];
  }
}
