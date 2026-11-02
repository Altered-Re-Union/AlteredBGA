<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_ClothCocoon extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_24_R1',
      'asset' => 'ALT_CORE_B_LY_24_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Cloth Cocoon'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Nobody expects... the cloth dancer!'),
      'artist' => 'Zero Wen',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Discard target <FLEETING_CHAR>, <ANCHORED> or <ASLEEP> Character.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'statuses' => [FLEETING, ANCHORED, ASLEEP],
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::ACTION(DISCARD, []),
        ])
      ),
    ];
  }
}
