<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_MeditationTraining extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_25_R1',
      'asset' => 'ALT_CORE_B_MU_25_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Meditation Training'),
      'typeline' => clienttranslate('Spell - Boon'),
      'type' => SPELL,
      'subtypes' => [BOON],
      'effectDesc' => clienttranslate(
        'Target Character with Hand Cost {3} or less gains <ANCHORED>. (During Rest, it doesn\'t go to Reserve and it loses Anchored.)'
      ),
      'supportDesc' => clienttranslate(
        '#{D} : The next Character you play this turn gains 1 boost.# (Discard me from Reserve to do this.)'
      ),
      'supportIcon' => 'discard',
      'flavorText' => clienttranslate('Don\'t think you are, know you are.'),
      'artist' => 'HuoMiao Studio',

      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'effect' => FT::GAIN(EFFECT, ANCHORED)]),
      'effectSupport' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains1Boost']),
    ];
  }
}
