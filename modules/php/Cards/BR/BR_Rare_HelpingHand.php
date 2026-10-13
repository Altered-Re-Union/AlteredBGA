<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_HelpingHand extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_25_R1',
      'asset' => 'ALT_CORE_B_BR_25_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Helping Hand'),
      'typeline' => clienttranslate('Spell - Boon'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Never gonna give you up.'),
      'artist' => 'Edward Cheekokseang',
      'subtypes' => [BOON],
      'effectDesc' => clienttranslate('#$<FLEETING>.#  Target Character gains #2 boosts# and loses <FLEETING_CHAR>.'),
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'effect' => FT::SEQ(FT::GAIN(EFFECT, BOOST, 2), FT::LOOSE(EFFECT, FLEETING)),
        ])
      ),
    ];
  }
}
