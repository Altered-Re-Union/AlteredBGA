<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_OrdisGatekeeper extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_13_R1',
      'asset' => 'ALT_CORE_B_OR_13_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ordis Gatekeeper'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{J} Create an <ORDIS_RECRUIT> Soldier token in #each of your# Expeditions.'),
      'artist' => 'Atanas Lozanski',
      'flavorText' => clienttranslate(
        'The Aegis Sentinel opened the door and stepped aside to let her through, acknowledging her with a nod as she passed.'
      ),

      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['mountain'],
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
        ])
      ),
    ];
  }
}
