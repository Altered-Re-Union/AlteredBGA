<?php

namespace ALT\Cards\BR;

class BR_Rare_Achilles extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_21_R1',
      'asset' => 'ALT_CORE_B_BR_21_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Achilles'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Many things lie between us—shadowy mountains and sounding sea."'),
      'artist' => 'Taras Susak',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('#$<TOUGH_2>.#'),
      'forest' => 5,
      'mountain' => 6,
      'ocean' => 6,
      'costHand' => 5,
      'costReserve' => 5,
      'changedStats' => ['mountain', 'ocean'],
      'tough' => 2,
    ];
  }
}
