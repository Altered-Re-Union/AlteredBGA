<?php
namespace ALT\Cards\LY;

class LY_Common_ScheriasRoyalty extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_133_C',
      'asset' => 'ALT_FUGUE_B_LY_133_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Scheria\'s Royalty'),
      'typeline' => clienttranslate('Character - Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Your journey will be arduous. Take this respite, and enjoy our hospitality."'),
      'artist' => 'Zero Wen',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE],
      'forest' => 3,
      'mountain' => 0,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
    ];
  }
}
