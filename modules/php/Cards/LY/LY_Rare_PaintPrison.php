<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_PaintPrison extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_26_R1',
      'asset' => 'ALT_CORE_B_LY_26_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Paint Prison'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Your problem is a lack of perspective.'),
      'artist' => 'Justice Wong',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate(
        '$<FLEETING>.  You may discard a card from your Reserve to reduce my cost by #{1}#.  Return target Character or Permanent to the top of its owner\'s deck.'
      ),

      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costHand', 'costReserve'],
      'costReductionDiscard' => 1,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'effect' => FT::ACTION(DISCARD, ['destination' => 'topOfDeck']),
        ])
      ),
    ];
  }
}
