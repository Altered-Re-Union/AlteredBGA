<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_RidetheBifrost extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_28_R1',
      'asset' => 'ALT_CORE_B_LY_28_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ride the Bifröst'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'flavorText' => clienttranslate('The Lyra never play by the rules.'),
      'artist' => 'Fahmi Fauzi',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate(
        '$<FLEETING>.  All Characters #controlled by target player# switch Expeditions. (They leave their Expeditions and join their controller\'s other Expedition.)'
      ),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(MOVE_CARD, ['player' => ME, 'cards' => ALL]),
        ])
      ),
    ];
  }
}
