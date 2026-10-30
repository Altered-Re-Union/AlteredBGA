<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_PoseidonsFury extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_143_C',
      'asset' => 'ALT_FUGUE_B_AX_143_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Poseidon\'s Fury'),
      'typeline' => clienttranslate('Spell'),
      'type' => SPELL,
      'artist' => 'Kevin Sidharta',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('$<FLEETING>.  Send target Character with a statistic of 4 or less {O} to Reserve.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::SEQ(FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxStatistic' => 4,
          'maxStatisticBiome' => OCEAN,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ])),
    ];
  }
}
