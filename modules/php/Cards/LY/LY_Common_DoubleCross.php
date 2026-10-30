<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_DoubleCross extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_143_C',
      'asset' => 'ALT_FUGUE_B_LY_143_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Double-Cross'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'extension' => 'NEJ',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  Draw a card, then each player with more than three cards in their hand discards down to three cards in hand.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(DRAW, ['players' => ME]),
        FT::ACTION(DISCARD, ['source' => HAND, 'downTo' => 3, 'eachPlayer' => true]),
      ),
    ];
  }
}
