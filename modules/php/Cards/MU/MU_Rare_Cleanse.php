<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_Cleanse extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);

    $this->properties = [
      'uid' => 'ALT_EOLE_B_LY_118_R2',
      'asset' => 'ALT_EOLE_B_LY_118_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate("Cleanse"),
      'typeline' => clienttranslate("Spell - Disruption"),
      'type' => SPELL,
      'flavorText'  => clienttranslate('"What do you think of my smoldering eyes?"'),
      'artist' => "Victor Canton",
      'extension' => 'ROC',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate(
        'Roll a die. #On a 1-3, I gain <FLEETING>.# Then, you may send to Reserve target Character with Base Cost {X} or less, where X is the result. (Base Cost is the Reserve Cost if it\'s Fleeting, or the Hand Cost if not.)'
      ),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::ACTION(ROLL_DIE, [
          // Disjoint bands only: `1-3` and `1+` both match rolls 1–3 → RollDie duplicate error.
          // Two separate TARGET trees: RollDie::actRollDie mutates `die` via updateTree in place.
          'effect' => [
            '1-3' => FT::SEQ(
              FT::GAIN(ME, FLEETING),
              FT::ACTION(TARGET, [
                'upTo' => true,
                'maxBaseCost' => 'die',
                'effect' => FT::DISCARD_TO_RESERVE(),
              ])
            ),
            '4+' => FT::ACTION(TARGET, [
              'upTo' => true,
              'maxBaseCost' => 'die',
              'effect' => FT::DISCARD_TO_RESERVE(),
            ]),
          ],
        ])
      ),
    ];
  }
}
