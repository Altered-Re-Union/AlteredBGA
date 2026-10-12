<?php
namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_DoubleCross extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_143_R1',
      'asset' => 'ALT_FUGUE_B_LY_143_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Double-Cross'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Are the Oneiroi truly your allies ?'),
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  Draw a card, then each player with more than three cards in their hand discards down to three cards in hand.  #Each player with more than two cards in their Reserve discards down to two cards in Reserve.#'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(DRAW, ['players' => ME]),
        FT::ACTION(DISCARD, ['source' => HAND, 'downTo' => 3, 'eachPlayer' => true]),
        FT::ACTION(DISCARD, ['source' => RESERVE, 'downTo' => 2, 'eachPlayer' => true]),
      ),
    ];
  }
}
