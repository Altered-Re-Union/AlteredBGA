<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_Nurture extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_27_R1',
      'asset' => 'ALT_CORE_B_MU_27_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Nurture'),
      'type' => SPELL,
      'subtypes' => [BOON],
      'effectDesc' => clienttranslate('Up to two target Characters each gain #2 boosts$<BB>#.'),
      'typeline' => clienttranslate('Spell - Boon'),
      'flavorText' => clienttranslate('When the sun shines, we\'ll shine together.'),
      'artist' => 'Zero Wen',

      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(TARGET, ['upTo' => true, 'n' => 2, 'effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 2])]),
    ];
  }
}
