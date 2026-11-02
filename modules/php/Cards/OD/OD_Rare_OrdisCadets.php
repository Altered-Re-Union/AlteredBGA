<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_OrdisCadets extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_06_R1',
      'asset' => 'ALT_CORE_B_OR_06_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ordis Cadets'),
      'typeline' => clienttranslate('Character - Apprentice Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Together they learn, and together they\'ll protect.'),
      'artist' => 'Anh Tung',
      'subtypes' => [APPRENTICE, SOLDIER],
      'effectDesc' => clienttranslate('{J} Create an <ORDIS_RECRUIT> Soldier token in #your other Expedition#.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['ocean'],
      'effectPlayed' => FT::ACTION(INVOKE_TOKEN, [
        'pId' => 'source',
        'tokenType' => 'OD_Common_OrdisRecruit',
        'targetLocation' => ['oppositeSource'],
      ]),
    ];
  }
}
