<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_SpyCraft extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_22_R1',
      'asset' => 'ALT_CORE_B_YZ_22_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Spy Craft'),
      'type' => SPELL,
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<SABOTAGE>, then $<RESUPPLY_LOW>.'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'flavorText' => clienttranslate('This message will self-destruct in five seconds.'),
      'artist' => 'Nestor Papatriantafyllou',

      'costHand' => 2,
      'costReserve' => 3,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
        FT::ACTION(RESUPPLY, [])
      ),
    ];
  }
}
