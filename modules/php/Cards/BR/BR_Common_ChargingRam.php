<?php

namespace ALT\Cards\BR;

class BR_Common_ChargingRam extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_131_C',
      'asset' => 'ALT_FUGUE_B_BR_131_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Charging Ram'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('Polyphemus\' gluttony nearly wiped out the species...but to him, that\'s the sheep\'s problem.'),
      'artist' => 'Julien Carrasco',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL],
      'forest' => 0,
      'mountain' => 2,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 1,
    ];
  }
}
