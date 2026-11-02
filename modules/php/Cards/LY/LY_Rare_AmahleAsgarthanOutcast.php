<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_AmahleAsgarthanOutcast extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_19_R1',
      'asset' => 'ALT_CORE_B_LY_19_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Amahle, Asgarthan Outcast'),
      'typeline' => clienttranslate('Character - Scholar'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'The old world is dying, and the new world struggles to be born: now is the time of monsters.'
      ),
      'artist' => 'Khoa Viet',
      'subtypes' => [SCHOLAR],
      'effectDesc' => clienttranslate('{J} You may discard #any number of cards# from your Reserve to #draw that many cards#.'),
      'forest' => 5,
      'mountain' => 5,
      'ocean' => 5,
      'costHand' => 5,
      'costReserve' => 5,
      'effectPlayed' => FT::ACTION(DISCARD_DO, ['effect' => FT::ACTION(DRAW, ['players' => ME, 'n' => 'X'])]),
    ];
  }
}
