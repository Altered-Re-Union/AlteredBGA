<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_Harvest extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_29_R1',
      'asset' => 'ALT_CORE_B_MU_29_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Harvest'),
      'typeline' => clienttranslate('Spell - Conjuration'),
      'type' => SPELL,
      'flavorText' => clienttranslate('The thankful receiver bears a plentiful harvest.'),
      'artist' => 'Ba Vo',
      'subtypes' => [CONJURATION],
      'effectDesc' => clienttranslate('$<RESUPPLY>, #then <RESUPPLY_LOW> again#.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand'],
      'effectPlayed' => FT::SEQ(FT::ACTION(RESUPPLY, []), FT::ACTION(RESUPPLY, [])),
    ];
  }
}
