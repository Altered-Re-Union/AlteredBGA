<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_LyraFestival extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_29_R1',
      'asset' => 'ALT_CORE_B_LY_29_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lyra Festival'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate(
        'When the time of the Kalann Mae comes, the Lyra all feel the call to a single place, where they will bring a masterpiece to life.'
      ),
      'artist' => 'Fahmi Fauzi',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate(
        '#{J} Target Character gains <FLEETING>, <ANCHORED> or <ASLEEP>.#  At Dusk, if you control a <FLEETING> Character, another <ANCHORED> Character and yet another <ASLEEP> Character — You win the game.'
      ),
      'costHand' => 4,
      'costReserve' => 4,
      'effectPlayed' => FT::ACTION(TARGET, [
        'effect' => FT::XOR(FT::GAIN(EFFECT, FLEETING), FT::GAIN(EFFECT, ANCHORED), FT::GAIN(EFFECT, ASLEEP)),
      ]),
      'effectPassive' => [
        'BeforeDusk' => [
          'condition' => 'hasControlFleetingAnchoredAsleep',
          'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'instantWin']),
        ],
      ],
    ];
  }
}
