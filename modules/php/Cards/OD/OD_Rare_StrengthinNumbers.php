<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_StrengthinNumbers extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_144_R1',
      'asset' => 'ALT_FUGUE_B_OR_144_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Strength in Numbers'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Gamon Studio',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  #Create an Ordis Recruit 1/1/1 Soldier token in target Expedition. Then,# target a Character #facing it# with no statistic higher than the number of Soldiers in your Expeditions. Send it to Reserve.'),
      'costHand' => 2,
      'costReserve' => 2, 
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
        ]),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxStatistic' => 'soldiersInExpeditions',
          'targetLocation' => ['opponentSource'],
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ),
    ];
  }
}
