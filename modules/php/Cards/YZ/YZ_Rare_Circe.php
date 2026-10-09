<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_Circe extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_138_R1',
      'asset' => 'ALT_FUGUE_B_YZ_138_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Circe'),
      'typeline' => clienttranslate('Character - Mage'),
      'type' => CHARACTER,
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [MAGE],
      'effectDesc' => clienttranslate('{H} #Each player# sacrifices a Character. Then, create a Woollyback 1/1/1 Animal token in each of those Characters\' Expeditions.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 1,
      'changedStats' => ['costHand'],
      'effectHand' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerSacrificeWoollyback']),
    ];
  }
}
