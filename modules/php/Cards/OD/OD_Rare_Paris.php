<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_Paris extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_133_R1',
      'asset' => 'ALT_FUGUE_B_OR_133_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Paris'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{R} Create an <ORDIS_RECRUIT> Soldier token in my Expedition.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 2,
      'changedStats' => ['forest'],
      'effectReserve' => FT::ACTION(INVOKE_TOKEN, [
        'pId' => 'source',
        'tokenType' => 'OD_Common_OrdisRecruit',
        'targetLocation' => ['source'],
      ]),
    ];
  }
}
