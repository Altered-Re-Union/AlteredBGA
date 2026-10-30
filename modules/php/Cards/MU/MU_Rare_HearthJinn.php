<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_HearthJinn extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_138_R2',
      'asset' => 'ALT_FUGUE_B_BR_138_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
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
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['ocean'],
      'effectReserve' => FT::ACTION(DISCARD, [
        'cardId' => ME, 
        'destination' => MANA, 
        'tapped' => true
      ], ['optional' => true]),
    ];
  }
}
