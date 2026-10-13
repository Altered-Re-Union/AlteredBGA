<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_LyraThespian extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_05_R1',
      'asset' => 'ALT_CORE_B_LY_05_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lyra Thespian'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"All the world’s a stage."'),
      'artist' => 'Rémi Jacquot',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate(
        '{J} If you have three or more base statistics of 0 among Characters you control, I gain #2 boosts#.'
      ),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boost23Stat0']),
    ];
  }
}
