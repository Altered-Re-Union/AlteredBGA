<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_EntrancingFragrance extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_144_C',
      'asset' => 'ALT_FUGUE_B_LY_144_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Entrancing Fragrance'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Fahmi Fauzi',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Choose one:  • Send to Reserve target Character with no statistic over 2.  • Discard target Permanent with Base Cost {3} or less.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, TOKEN],
            'maxStatistic' => 2, 
            'effect' => FT::DISCARD_TO_RESERVE()
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'maxBaseCost' => 3,
            'effect' => FT::ACTION(DISCARD, []),
          ])
        ),
      ),
    ];
  }
}
