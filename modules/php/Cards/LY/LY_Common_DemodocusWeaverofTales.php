<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_DemodocusWeaverofTales extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_136_C',
      'asset' => 'ALT_FUGUE_B_LY_136_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Demodocus, Weaver of Tales'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'artist' => 'Jamin Amaral Fernandez',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('{H} Roll a die. On a 4+, you may have target Character gain Fleeting. (If it would be sent to Reserve, discard it instead.)'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '4+' => FT::ACTION(TARGET, [
            'targetType' => [CHARACTER],
            'effect' => FT::GAIN(EFFECT, FLEETING)])
        ],
      ]),
    ];
  }
}
