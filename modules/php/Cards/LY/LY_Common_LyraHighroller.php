<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_LyraHighroller extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_140_C',
      'asset' => 'ALT_FUGUE_B_LY_140_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Lyra Highroller'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Sometimes, you have to play big.'),
      'artist' => 'Benoit Barraqué-Currie',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('{H} Roll a die. On a 1, I gain Fleeting.'),
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'effectHand' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '1' => FT::GAIN(ME, FLEETING),
        ],
      ]),
    ];
  }
}
