<?php

namespace ALT\Cards\BR;

class BR_Common_Booda extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_31_C',
      'asset' => 'ALT_CORE_B_BR_31_C',

      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Booda'),
      'type' => CHARACTER,
      'subtypes' => [COMPANION],
      'effectDesc' => clienttranslate('(If I leave the Expedition zone, remove me from the game.)'),
      'flavorText' => clienttranslate(
        'A Hero can practice Alteration through their Companion, no matter the distance between them.'
      ),
      'artist' => 'Edward Cheekokseang',

      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'token' => true,
      'costHand' => 0,
      'costReserve' => 0,
      'typeline' => clienttranslate('Token Character - Companion'),
    ];
  }
}
