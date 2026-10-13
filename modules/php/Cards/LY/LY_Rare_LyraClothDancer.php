<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_LyraClothDancer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_14_R1',
      'asset' => 'ALT_CORE_B_LY_14_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lyra Cloth Dancer'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Hope you\'re not afraid of heights!'),
      'artist' => 'Fori Y.',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate(
        '{H} #Each Character controlled by target player# gains <FLEETING_CHAR>. (If it would be sent to Reserve, discard it instead.)'
      ),
      'forest' => 0,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['mountain'],
      'effectHand' => FT::ACTION(TARGET_PLAYER, [
        'opponentsOnly' => false,
        'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'fleetingAllCharacters']),
      ]),
    ];
  }
}
