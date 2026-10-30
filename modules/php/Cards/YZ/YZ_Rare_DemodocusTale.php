<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_DemodocusTale extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_142_R2',
      'asset' => 'ALT_FUGUE_B_LY_142_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Demodocus\' Tale'),
      'typeline' => clienttranslate('Spell - Song'),
      'type' => SPELL,
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [SONG],
      'effectDesc' => clienttranslate('Roll a die. #On a 6+, you may choose both, otherwise choose one:#  • Target Character gains 1 boost.  • Target Character gains Fleeting.'),
      'costHand' => 1,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '6+' => FT::OR(
            FT::ACTION(TARGET, [
              'effect' => FT::GAIN(EFFECT, BOOST),
            ]),
            FT::ACTION(TARGET, [
              'effect' => FT::GAIN(EFFECT, FLEETING),
            ]),
          ),
          '1-5' => FT::XOR(
            FT::ACTION(TARGET, [
              'effect' => FT::GAIN(EFFECT, BOOST),
            ]),
            FT::ACTION(TARGET, [
              'effect' => FT::GAIN(EFFECT, FLEETING),
            ]),
          ),
        ],
      ]),
    ];
  }
}
