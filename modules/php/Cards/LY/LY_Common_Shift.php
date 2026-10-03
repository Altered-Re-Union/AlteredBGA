<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_Shift extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_148_C',
      'asset' => 'ALT_FUGUE_B_LY_148_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Shift'),
      'typeline' => clienttranslate('Character - Companion'),
      'type' => CHARACTER,
      'artist' => 'Zero Wen',
      'extension' => 'NEJ',
      'subtypes' => [COMPANION],
      'effectDesc' => clienttranslate('{R} Roll a die. On a 4+, I gain 1 boost. (I\'m created in Reserve. You can play me in an Expedition. Remove me from the game if I would go anywhere else.)'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costReserve' => 1,
      'token' => true,
      'effectReserve' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '4+' => FT::GAIN(ME, BOOST),
        ],
      ]),
    ];
  }
}
