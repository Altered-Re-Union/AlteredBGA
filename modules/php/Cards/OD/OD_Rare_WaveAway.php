<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_WaveAway extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_141_R2',
      'asset' => 'ALT_FUGUE_B_YZ_141_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Wave Away'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Ahn Tung',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  #Discard# target Character with Base Cost #{1} or less#.'),
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 1,
          'effect' => FT::ACTION(DISCARD, []),
        ])
      ),
    ];
  }
}
