<?php
namespace ALT\Cards\OD;

class OD_Common_TriremeQuartermaster extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_134_C',
      'asset' => 'ALT_FUGUE_B_OR_134_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Trireme Quartermaster'),
      'typeline' => clienttranslate('Character - Soldier, Trainer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Look sharp, scallywags! I see our next adventure on the horizon!'),
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, TRAINER],
      'forest' => 0,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
    ];
  }
}
