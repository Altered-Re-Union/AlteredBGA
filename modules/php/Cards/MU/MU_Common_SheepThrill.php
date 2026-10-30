<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_SheepThrill extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_144_C',
      'asset' => 'ALT_FUGUE_B_MU_144_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Sheep Thrill'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Kevin Sidharta Vo',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Discard target Character with no statistic higher than 4, then create a <WOOLLYBACK> Animal token in its Expedition.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' =>  FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, TOKEN],
            'maxStatistic' => 4,
            'effect' => FT::SEQ(
                FT::ACTION(DISCARD, []),
                FT::ACTION(INVOKE_TOKEN, [
                    'pId' => 'source',
                    'tokenType' => 'MU_Common_Woollyback',
                    'targetLocation' => ['discardedSource'],
                ]),
            )
        ]),
      ),
    ];
  }
}
