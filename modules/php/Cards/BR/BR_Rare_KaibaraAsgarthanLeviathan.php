<?php

namespace ALT\Cards\BR;

class BR_Rare_KaibaraAsgarthanLeviathan extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_24_R1',
      'asset' => 'ALT_CORE_B_BR_24_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kaibara, Asgarthan Leviathan'),
      'typeline' => clienttranslate('Character - Leviathan'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('For hundreds of years, Kaibara has protected Asagartha.'),
      'artist' => 'Fahmi Fauzi',
      'subtypes' => [LEVIATHAN],
      'effectDesc' => clienttranslate(
        '$<GIGANTIC>.  $<TOUGH_X>, where X is the number of regions between your Hero and Companion. (If they are adjacent, X equals 0.)'
      ),
      'forest' => 8,
      'mountain' => 8,
      'ocean' => 8,
      'costHand' => 9,
      'costReserve' => 9,
      'changedStats' => ['forest', 'mountain', 'ocean', 'costHand', 'costReserve'],
      'gigantic' => true,
      'dynamicTough' => 'region',
    ];
  }
}
