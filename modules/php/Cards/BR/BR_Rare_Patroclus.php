<?php
namespace ALT\Cards\BR;

class BR_Rare_Patroclus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_132_R1',
      'asset' => 'ALT_FUGUE_B_BR_132_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Patroclus'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('Achilles and Patroclus shared many things, including courage, tenacity, and a tent.'),
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('#<TOUGH_CHA_P_1>.#'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['ocean'],
      'tough' => 1,
    ];
  }
}
