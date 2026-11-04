<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_WaveAway extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_141_C',
      'asset' => 'ALT_FUGUE_B_YZ_141_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Wave Away'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Ahn Tung',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Target a Character with Base Cost {2} or less. If you have six or more Mana Orbs, discard it, otherwise send it to Reserve.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 2,
          'effect' => FT::ACTION(CHECK_CONDITION, [
            'condition' => 'hasXMana:6',
            'effect' => FT::ACTION(DISCARD, []),
            'oppositeEffect' => FT::DISCARD_TO_RESERVE(),
          ]),
        ])
      ),
    ];
  }
}
