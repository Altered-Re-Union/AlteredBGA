<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_HelenofTroy extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_140_R2',
      'asset' => 'ALT_FUGUE_B_OR_140_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Helen of Troy'),
      'typeline' => clienttranslate('Character - Citizen Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Ten years of war, all over the beauty of one individual.'),
      'artist' => 'Nathan Maneval',
      'extension' => 'NEJ',
      'subtypes' => [CITIZEN, NOBLE],
      'effectDesc' => clienttranslate('#{H}# Create an <ORDIS_RECRUIT> Soldier token in each of your Expeditions.'),
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 1,
      'changedStats' => ['mountain', 'costReserve'],
      'effectHand' => FT::SEQ(
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => [STORM_RIGHT],
        ]),
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => [STORM_LEFT],
          'moreThan1' => true,
        ]),
      ),
    ];
  }
}
