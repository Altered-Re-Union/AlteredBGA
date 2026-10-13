<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_BeautySleep extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_28_R1',
      'asset' => 'ALT_CORE_B_MU_28_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Beauty Sleep'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Yet beware of splinters of flax.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate(
        'Target Character gains <ASLEEP>. #You may give it 2 boosts.# (During Dusk, ignore its statistics. During Rest, it doesn\'t go to Reserve and it loses Asleep.)'
      ),
      'costHand' => 1,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(TARGET, [
        'effect' => FT::SEQ(FT::GAIN(EFFECT, ASLEEP), FT::SEQ_OPTIONAL(FT::GAIN(EFFECT, BOOST, 2))),
      ]),
    ];
  }
}
