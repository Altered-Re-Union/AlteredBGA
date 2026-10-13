<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_HuaMulan extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_12_R1',
      'asset' => 'ALT_CORE_B_BR_12_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Hua Mulan'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Her determination has yet to meet its match in this world.'),
      'artist' => 'Zero Wen',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{R} I lose <FLEETING_CHAR>.'),
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain', 'ocean', 'costHand', 'costReserve'],
      'effectReserve' => FT::LOOSE($this, FLEETING),
    ];
  }
}
