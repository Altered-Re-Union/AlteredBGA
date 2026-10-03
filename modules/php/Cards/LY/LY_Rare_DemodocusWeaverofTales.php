<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_DemodocusWeaverofTales extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_136_R1',
      'asset' => 'ALT_FUGUE_B_LY_136_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Demodocus, Weaver of Tales'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'artist' => 'Jamin Amaral Fernandez',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('{R} Roll a die. You may target a Character #with Base Cost less than or equal to the die\'s result#. It gains $<FLEETING>.'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['ocean'],
      'effectReserve' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '1+' => FT::ACTION(TARGET, [
            'targetType' => [CHARACTER],
            'upTo' => true,
            'maxBaseCost' => 'die',
            'effect' => FT::GAIN(EFFECT, FLEETING)]),
        ],
      ]),
    ];
  }
}
