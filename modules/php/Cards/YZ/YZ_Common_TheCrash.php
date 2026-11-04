<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_TheCrash extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_142_C',
      'asset' => 'ALT_FUGUE_B_YZ_142_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('The Crash'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('"Even if we survive, we cannot cross the clouds again."'),
      'artist' => 'Kevin Sidharta',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Choose one:  • Discard target Permanent with Base Cost {3} or less.  • Discard target token.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'maxBaseCost' => 3,
            'effect' => FT::ACTION(DISCARD, []),
          ]),
          FT::ACTION(TARGET, [
            'onlyToken' => true,
            'targetType' => [CHARACTER, PERMANENT],
            'targetLocation' => [...STORMS, LANDMARK],
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        )
      ),
    ];
  }
}
