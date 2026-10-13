<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_MagicalTraining extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_19_R1',
      'asset' => 'ALT_CORE_B_YZ_19_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Magical Training'),
      'typeline' => clienttranslate('Spell - Conjuration'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Without practice, the gift of Alteration will come to nothing.'),
      'artist' => 'Zero Wen',
      'subtypes' => [CONJURATION],
      'effectDesc' => clienttranslate('Draw a card.'),
      'costHand' => 1,
      'costReserve' => 2,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(DRAW, ['players' => ME]),
    ];
  }
}
