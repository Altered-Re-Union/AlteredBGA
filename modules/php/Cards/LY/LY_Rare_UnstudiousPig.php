<?php
namespace ALT\Cards\LY;

class LY_Rare_UnstudiousPig extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_131_R1',
      'asset' => 'ALT_FUGUE_B_LY_131_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Unstudious Pig'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Fasano loves his new form...but life as a pig is anything but easy." - Nif-Nif'),
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL],
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['mountain'],
    ];
  }
}
