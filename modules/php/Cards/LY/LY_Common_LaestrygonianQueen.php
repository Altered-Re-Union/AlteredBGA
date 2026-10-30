<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_LaestrygonianQueen extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_139_C',
      'asset' => 'ALT_FUGUE_B_LY_139_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Laestrygonian Queen'),
      'typeline' => clienttranslate('Character - Titan'),
      'type' => CHARACTER,
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'subtypes' => [TITAN],
      'effectDesc' => clienttranslate('$<GIGANTIC>. (I\'m considered present in each of your Expeditions.)  {J} Roll a die. I gain X boosts, where X is the result.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 5,
      'costReserve' => 5,
      'gigantic' => true,
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => ['1+' => FT::GAIN(ME, BOOST, 'die')],
      ]),
    ];
  }
}
