<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_MindApotheosis extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_27_R1',
      'asset' => 'ALT_CORE_B_LY_27_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Mind Apotheosis'),
      'typeline' => clienttranslate('Spell - Conjuration'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Who looks outside, dreams; who looks inside, awakes.'),
      'artist' => 'Zero Wen',
      'subtypes' => [CONJURATION],
      'effectDesc' => clienttranslate(
        '<FLEETING>.  Reveal the top four cards of your Deck. Choose up to two Characters from these cards and put them in your Expeditions. They gain <FLEETING>. Discard the other cards. (Don\'t activate any {h} abilities.)'
      ),
      'costHand' => 8,
      'costReserve' => 8,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(FT::GAIN(ME, FLEETING), FT::ACTION(SPECIAL_EFFECT, ['effect' => 'MindApotheosis'])),
    ];
  }
}
