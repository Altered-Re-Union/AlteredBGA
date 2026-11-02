<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_PhysicalTraining extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_26_R1',
      'asset' => 'ALT_CORE_B_BR_26_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Physical Training'),
      'typeline' => clienttranslate('Spell - Boon'),
      'type' => SPELL,
      'subtypes' => [BOON],
      'effectDesc' => clienttranslate('Target Character gains 3 boosts$<BB>.  #Draw a card.#'),
      'flavorText' => clienttranslate(
        '100 crunches, 100 push-ups, 100 squats, a 10-km run and a desire to prove yourself. Just your typical morning of Bravos training.'
      ),
      'artist' => 'Polar Engine',

      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['costHand'],
      'effectPlayed' => FT::SEQ(
        FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 3])]),
        FT::ACTION(DRAW, ['players' => ME])
      ),
    ];
  }
}
