<?php
namespace ALT\Cards\LY;

class LY_Common_UnstudiousPig extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_131_C',
      'asset' => 'ALT_FUGUE_B_LY_131_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Unstudious Pig'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Fasano loves his new form...but life as a pig is anything but easy." - Nif-Nif'),
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL],
      'forest' => 1,
      'mountain' => 0,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
    ];
  }
}
