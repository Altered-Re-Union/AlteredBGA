<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_LaestrygonianQueen extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_139_R1',
      'asset' => 'ALT_FUGUE_B_LY_139_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Laestrygonian Queen'),
      'typeline' => clienttranslate('Character - Titan'),
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [TITAN],
      'effectDesc' => clienttranslate('$<GIGANTIC>#, <TOUGH_CHA_P_1>#.  #If you would roll one or more dice, instead roll that many dice plus one and ignore the roll of your choice.#  {J} Roll a die. I gain X boosts, where X is the result.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 6,
      'costReserve' => 6,
      'changedStats' => ['costHand', 'costReserve'],
      'gigantic' => true,
      'tough' => 1,
      'addDice' => 1,
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => ['1+' => FT::GAIN(ME, BOOST, 'die')],
      ]),
    ];
  }
}
