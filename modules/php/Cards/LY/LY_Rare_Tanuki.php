<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_Tanuki extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_09_R1',
      'asset' => 'ALT_CORE_B_LY_09_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Tanuki'),
      'typeline' => clienttranslate('Character - Spirit'),
      'type' => CHARACTER,
      'subtypes' => [SPIRIT],
      'effectDesc' => clienttranslate('{H} $<SABOTAGE>.  #{R} Roll a die. On a 4+, <SABOTAGE_LOW>.#'),
      'flavorText' => clienttranslate('"Pom! Pompoko, pom!"'),
      'artist' => 'Matteo Spirito',

      'forest' => 3,
      'mountain' => 3,
      'ocean' => 0,
      'costHand' => 3,
      'costReserve' => 3,

      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
      'effectReserve' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '4+' => FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        ],
      ]),
    ];
  }
}
