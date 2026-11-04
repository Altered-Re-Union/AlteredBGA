<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_LyraGambler extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_135_R1',
      'asset' => 'ALT_FUGUE_B_LY_135_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lyra Gambler'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Every gamble carries some risk.'),
      'artist' => 'Damian Audino',
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{H} Roll a die. On a:  #• 6+, I gain Anchored.#  • 1, I gain $<FLEETING>.'),
      'forest' => 2,
      'mountain' => 3,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '1' => FT::GAIN(ME, FLEETING),
          '6+' => FT::GAIN(ME, ANCHORED),
        ],
      ]),
    ];
  }
}
