<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_Athena extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_18_R1',
      'asset' => 'ALT_CORE_B_AX_18_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Athena'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"I’ve lifted the mist from off your eyes that’s blurred them up to now."'),
      'artist' => 'Zero Wen',
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate(
        '#{H} If you control two or more Landmarks, I gain 2 boosts.#  {R} If you control two or more Landmarks, I lose <FLEETING_CHAR>.'
      ),
      'forest' => 3,
      'mountain' => 4,
      'ocean' => 4,
      'costHand' => 5,
      'costReserve' => 3,
      'effectReserve' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasControl:landmark:2',
        'effect' => FT::LOOSE(ME, FLEETING),
      ]),
      'effectHand' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasControl:landmark:2',
        'effect' => FT::GAIN(ME, BOOST, 2),
      ]),
    ];
  }
}
