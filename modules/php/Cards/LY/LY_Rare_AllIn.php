<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_AllIn extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_25_R1',
      'asset' => 'ALT_CORE_B_LY_25_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('All In!'),
      'typeline' => clienttranslate('Spell - Boon'),
      'type' => SPELL,
      'flavorText' => clienttranslate('There\'s a time to be cautious, and a time to bet it all!'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [BOON],
      'effectDesc' => clienttranslate(
        'Roll a die. #You may discard a card from your Reserve to increase the result by 2.# Target Character gains X boosts, where X is the final result.'
      ),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'canDiscard' => true,
        'effect' => ['1+' => FT::ACTION(TARGET, ['effect' => FT::GAIN(EFFECT, BOOST, 'die')])],
      ]),
    ];
  }
}
