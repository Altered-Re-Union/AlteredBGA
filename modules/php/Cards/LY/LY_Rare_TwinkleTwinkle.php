<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_TwinkleTwinkle extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_23_R1',
      'asset' => 'ALT_CORE_B_LY_23_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Twinkle Twinkle'),
      'typeline' => clienttranslate('Spell - Song'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Up above the world so high, like a diamond in the sky.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [SONG],
      'effectDesc' => clienttranslate(
        '#$<FLEETING>.#  #All Characters in target Expedition# gain <ASLEEP>. (This only affects one player\'s Characters. During Dusk, ignore their statistics. During Rest, they don\'t go to Reserve and they lose Asleep.)'
      ),
      'supportDesc' => clienttranslate(
        '{D} : The next card you play this turn costs {1} less. (Discard me from Reserve to do this.)'
      ),
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costHand', 'costReserve'],
      'supportIcon' => 'discard',
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET_EXPEDITION, ['effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'sleepingAllCharactersinExpedition'])])
      ),
      'effectSupport' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => ALL, 'reduction' => 1]],
      ],
    ];
  }
}
