<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_BravosTracer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_07_R1',
      'asset' => 'ALT_CORE_B_BR_07_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Bravos Tracer'),
      'type' => CHARACTER,
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{J} I gain <FLEETING_CHAR>. (If I would be sent to Reserve, discard me instead.)'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'flavorText' => clienttranslate('"I only feel alive when I hear the wind whistling in my ears."'),
      'artist' => 'Justice Wong',

      'forest' => 4,
      'mountain' => 3,
      'ocean' => 4,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['forest', 'ocean'],
      'effectPlayed' => FT::GAIN(ME, FLEETING),
    ];
  }
}
