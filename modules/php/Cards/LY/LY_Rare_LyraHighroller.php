<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_LyraHighroller extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_140_R1',
      'asset' => 'ALT_FUGUE_B_LY_140_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lyra Highroller'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Sometimes, you have to play big.'),
      'artist' => 'Benoit Barraqué-Currie',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('{J} Roll a die. On a:  #• 6+, I gain Anchored.#  • 1, I gain Fleeting.'),
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '6+' => FT::GAIN(ME, ANCHORED),
          '1' => FT::GAIN(ME, FLEETING),
        ],
      ]),
    ];
  }
}
