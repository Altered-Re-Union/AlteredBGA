<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_ACappellaTraining extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_22_R1',
      'asset' => 'ALT_CORE_B_LY_22_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('A Cappella Training'),
      'typeline' => clienttranslate('Spell - Song'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Thank you for being my metronome!'),
      'artist' => 'Zero Wen',
      'subtypes' => [SONG],
      'effectDesc' => clienttranslate(
        '#<FLEETING>.#  Target Character gains <FLEETING_CHAR>. (If it would be sent to Reserve, discard it instead.)  #Draw a card.#'
      ),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, ['effect' => FT::GAIN(EFFECT, FLEETING)]),
        FT::ACTION(DRAW, ['players' => ME])
      ),
    ];
  }
}
