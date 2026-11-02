<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_CelestialBlast extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_27_R1',
      'asset' => 'ALT_CORE_B_YZ_27_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Celestial Blast'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('The sky cracked open in a thunderous wave as the dream was unleashed upon the world.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Discard up to two targets, Characters or Permanents.  #Draw a card.#'),
      'costHand' => 7,
      'costReserve' => 7,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'n' => 2,
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
        FT::ACTION(DRAW, ['players' => ME], ['pId' => 'source'])
      ),
    ];
  }
}
