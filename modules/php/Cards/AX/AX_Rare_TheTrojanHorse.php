<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_TheTrojanHorse extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_145_R2',
      'asset' => 'ALT_FUGUE_B_OR_145_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Trojan Horse'),
      'typeline' => clienttranslate('Character - Construction'),
      'type' => CHARACTER,
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION],
      'effectDesc' => clienttranslate('<DEFENDER_FS>.  {J} I #defect#.  When I leave the Expedition zone — My owner creates an <ORDIS_RECRUIT> Soldier token in each of their Expeditions.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 5,
      'costReserve' => 5,
      'defender' => true,
      'effectPlayed' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect', 'cardId' => ME]),
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
