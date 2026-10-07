<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_HearthJinn extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_138_C',
      'asset' => 'ALT_FUGUE_B_BR_138_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Hearth Jinn'),
      'typeline' => clienttranslate('Character - Elemental'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Keeping the flame alive...'),
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [ELEMENTAL],
      'effectDesc' => clienttranslate('{R} You may put me in your Mana zone (as an exhausted Mana Orb).'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 2,
      'effectReserve' => FT::ACTION(DISCARD, [
        'cardId' => ME, 
        'destination' => MANA, 
        'tapped' => true
      ], ['optional' => true]),
    ];
  }
}
