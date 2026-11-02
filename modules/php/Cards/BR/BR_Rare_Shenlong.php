<?php

namespace ALT\Cards\BR;

class BR_Rare_Shenlong extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_22_R1',
      'asset' => 'ALT_CORE_B_BR_22_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Shenlong'),
      'typeline' => clienttranslate('Character - Deity Dragon'),
      'type' => CHARACTER,
      'subtypes' => [DEITY, DRAGON],
      'effectDesc' => clienttranslate('#$<TOUGH_1>.#'),
      'flavorText' => clienttranslate('Be careful what you wish for. There are challenges that even the Bravos prefer to avoid.'),
      'artist' => 'HuoMiao Studio',

      'forest' => 9,
      'mountain' => 9,
      'ocean' => 9,
      'costHand' => 6,
      'costReserve' => 6,
      'changedStats' => ['forest', 'mountain', 'ocean'],
      'tough' => 1,
    ];
  }
}
