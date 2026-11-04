<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_StrengthinNumbers extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_144_C',
      'asset' => 'ALT_FUGUE_B_OR_144_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Strength in Numbers'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Gamon Studio',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Target a Character with no statistic higher than the number of Soldiers in your Expeditions. Send it to Reserve.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxStatistic' => 'soldiersInExpeditions',
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ),
    ];
  }
}
