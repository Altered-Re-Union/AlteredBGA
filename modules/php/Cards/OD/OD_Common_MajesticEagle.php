<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_MajesticEagle extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_131_C',
      'asset' => 'ALT_FUGUE_B_OR_131_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Majestic Eagle'),
      'typeline' => clienttranslate('Character - Soldier, Animal'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('It\'s time to spread your wings, take flight, and sail towards the horizon.'),
      'artist' => 'Kevin Sidharta',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, ANIMAL],
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 1,
    ];
  }
}
