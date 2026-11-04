<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_TheTrojanHorse extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_145_C',
      'asset' => 'ALT_FUGUE_B_OR_145_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('The Trojan Horse'),
      'typeline' => clienttranslate('Character - Construction'),
      'type' => CHARACTER,
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION],
      'effectDesc' => clienttranslate('<DEFENDER_FS>.  {J} I gain $<FLEETING> and I defect. (I join the Expedition facing me.)  When I leave the Expedition zone — My owner creates an <ORDIS_RECRUIT> Soldier token in each of their Expeditions.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 5,
      'costReserve' => 5,
      'defender' => true,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect', 'cardId' => ME]),
      ),
      'effectPassive' => [
        'LeaveExpedition' => [
          'output' => FT::SEQ(
            FT::ACTION(INVOKE_TOKEN, [
              'targetPlayer' => 'owner',
              'tokenType' => 'OD_Common_OrdisRecruit',
              'targetLocation' => [STORM_RIGHT],
            ]),
            FT::ACTION(INVOKE_TOKEN, [
              'targetPlayer' => 'owner',
              'tokenType' => 'OD_Common_OrdisRecruit',
              'targetLocation' => [STORM_LEFT],
              'moreThan1' => true,
            ]),
          ),
        ],
      ],
    ];
  }
}
