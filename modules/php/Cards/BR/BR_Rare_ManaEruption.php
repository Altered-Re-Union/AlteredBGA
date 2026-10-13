<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_ManaEruption extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_29_R1',
      'asset' => 'ALT_CORE_B_BR_29_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Mana Eruption'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate(
        'Flames fly from his hand as he falls towards the monster, leaving a trail of glowing fire behind him...'
      ),
      'artist' => 'Zero Wen',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate(
        '$<FLEETING>.  Discard one of your Mana Orbs. If you do, discard target Character or Permanent.'
      ),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [MANA],
          'targetType' => [CHARACTER, TOKEN, SPELL, PERMANENT],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
        FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN, PERMANENT], 'effect' => FT::ACTION(DISCARD, [])])
      ),
    ];
  }
}
