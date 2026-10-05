<?php
namespace ALT\Cards\AX;

class AX_Rare_Polyxena extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_134_R2',
      'asset' => 'ALT_FUGUE_B_YZ_134_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Polyxena'),
      'typeline' => clienttranslate('Character - Mage'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('The cost of war is measured in human lives.'),
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [MAGE],
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['forest'],
    ];
  }
}
