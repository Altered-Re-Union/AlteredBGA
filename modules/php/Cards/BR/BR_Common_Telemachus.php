<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_Telemachus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_134_C',
      'asset' => 'ALT_FUGUE_B_BR_134_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Telemachus'),
      'typeline' => clienttranslate('Character - Noble, Soldier'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('Daring the adventure is a destination in itself.'),
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE, SOLDIER],
      'effectDesc' => clienttranslate('{R} I gain 2 boosts.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 2,
      'effectReserve' => FT::GAIN(ME, BOOST, 2),
    ];
  }
}
