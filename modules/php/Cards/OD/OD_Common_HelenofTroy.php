<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_HelenofTroy extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_140_C',
      'asset' => 'ALT_FUGUE_B_OR_140_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Helen of Troy'),
      'typeline' => clienttranslate('Character - Citizen, Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Ten years of war, all over the beauty of one individual.'),
      'artist' => 'Nathan Maneval',
      'extension' => 'NEJ',
      'subtypes' => [CITIZEN, NOBLE],
      'effectDesc' => clienttranslate('{J} Create an <ORDIS_RECRUIT> Soldier token in each of your Expeditions.'),
      'forest' => 1,
      'mountain' => 0,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::SEQ(
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
