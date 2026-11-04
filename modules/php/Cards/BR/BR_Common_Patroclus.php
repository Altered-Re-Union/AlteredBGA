<?php
namespace ALT\Cards\BR;

class BR_Common_Patroclus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_132_C',
      'asset' => 'ALT_FUGUE_B_BR_132_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Patroclus'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('Achilles and Patroclus shared many things, including courage, tenacity, and a tent.'),
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 2,
    ];
  }
}
