<?php
namespace ALT\Cards\LY;

class LY_Rare_TriremeCaptain extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_135_R2',
      'asset' => 'ALT_FUGUE_B_BR_135_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Trireme Captain'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('Companions in your Expeditions are #Gigantic#.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costHand', 'costReserve'],
      'dynamicGigantic' => 'universalGiganticCompanion',
    ];
  }
}
