<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_LaestrygonianQueen extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_139_R2',
      'asset' => 'ALT_FUGUE_B_LY_139_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Laestrygonian Queen'),
      'typeline' => clienttranslate('Character - Titan'),
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [TITAN],
      'effectDesc' => clienttranslate('$<GIGANTIC>#, <TOUGH_CHA_P_1>#.  {J} Roll a die. I gain X boosts, where X is the result.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 5,
      'costReserve' => 5,
      'gigantic' => true,
      'tough' => 1,
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => ['1+' => FT::GAIN(ME, BOOST, 'die')],
      ]),
    ];
  }
}
