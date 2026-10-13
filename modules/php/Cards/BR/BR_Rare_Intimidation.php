<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_Intimidation extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_28_R1',
      'asset' => 'ALT_CORE_B_BR_28_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Intimidation'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('The terrible beast shrank and cowered before the might of the Bravos.'),
      'artist' => 'Nestor Papatriantafyllou',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate(
        '$<FLEETING>.  Return target Character or Permanent with Hand Cost #{5} or less# to its owner\'s hand.'
      ),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, ['maxHandCost' => 5, 'targetType' => [CHARACTER, TOKEN, PERMANENT], 'effect' => FT::RETURN_TO_HAND()])
      ),
    ];
  }
}
