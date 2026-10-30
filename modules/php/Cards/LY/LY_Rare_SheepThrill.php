<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_SheepThrill extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_144_R2',
      'asset' => 'ALT_FUGUE_B_MU_144_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Sheep Thrill'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Kevin Sidharta',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Discard target Character with no statistic #higher than 3#, then create a <WOOLLYBACK> Animal token in its Expedition.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'maxStatistic' => 3,
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
