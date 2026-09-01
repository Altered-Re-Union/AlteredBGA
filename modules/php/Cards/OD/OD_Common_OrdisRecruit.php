<?php

namespace ALT\Cards\OD;

class OD_Common_OrdisRecruit extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_32_C',
      'asset' => 'ALT_CORE_B_OR_31_C',

      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Ordis Recruit'),
      'type' => CHARACTER,
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('(If I leave the Expedition zone, remove me from the game.)'),
      'typeline' => clienttranslate('Token Character - Soldier'),
      'flavorText' => clienttranslate('MISSING FLAVOR'),
      'artist' => 'Jean-Baptiste Andrier',

      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'token' => true,
      'costReserve' => 0,
      'costHand' => 0,
    ];
  }
}
