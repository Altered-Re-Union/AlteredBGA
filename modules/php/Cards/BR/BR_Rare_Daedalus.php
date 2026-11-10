<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_Daedalus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_BISE_B_LY_53_R2',
      'asset'  => 'ALT_BISE_B_LY_53_R',

      'faction'  => FACTION_BR,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Daedalus"),
      'typeline' => clienttranslate("Character - Engineer"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('No one is truly lost, for all are exactly where fate wants them to be.'),
      'artist' => "Taras Susak",
      'extension' => 'WFTM',
      'subtypes'  => [ENGINEER],
      'effectDesc' => clienttranslate('#$<SCOUT_1> {1}.#  #{J}# Target another Character #in play# or in Reserve. Then, roll a die:  • On a 4+, we both gain 1 boost.  • On a 1-3, it gains 1 boost.'),
      'forest' => 0,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'scout' => 1,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE],
        'excludeSelf' => true,
        'effect' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1-3' => FT::GAIN(EFFECT, BOOST),
            '4+' => FT::SEQ(
              FT::GAIN(EFFECT, BOOST),
              FT::GAIN(ME, BOOST, 1, 99, true)
            ),
          ],
        ])
      ])
    ];
  }
}
