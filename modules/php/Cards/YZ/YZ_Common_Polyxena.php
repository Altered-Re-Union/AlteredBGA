<?php
namespace ALT\Cards\YZ;

class YZ_Common_Polyxena extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_134_C',
      'asset' => 'ALT_FUGUE_B_YZ_134_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Polyxena'),
      'typeline' => clienttranslate('Character - Mage'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('The cost of war is measured in human lives.'),
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [MAGE],
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
    ];
  }
}
