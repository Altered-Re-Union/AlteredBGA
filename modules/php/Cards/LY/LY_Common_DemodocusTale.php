<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_DemodocusTale extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_142_C',
      'asset' => 'ALT_FUGUE_B_LY_142_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Demodocus\' Tale'),
      'typeline' => clienttranslate('Spell - Song'),
      'type' => SPELL,
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [SONG],
      'effectDesc' => clienttranslate('Roll a die. On a:  • 4+, target Character gains 1 boost.  • 1-3, target Character gains Fleeting. (If it would be sent to Reserve, discard it instead.)'),
      'costHand' => 1,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '4+' => FT::ACTION(TARGET, [
            'effect' => FT::GAIN(EFFECT, BOOST),
          ]),
          '1-3' => FT::ACTION(TARGET, [
            'effect' => FT::GAIN(EFFECT, FLEETING),
          ]),
        ],
      ]),
    ];
  }
}
