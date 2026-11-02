<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_OpentheGates extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_26_R1',
      'asset' => 'ALT_CORE_B_OR_26_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Open the Gates'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate(
        'Create #four# <ORDIS_RECRUIT> Soldier tokens, #distributed as you choose among any number of target Expeditions#.'
      ),
      'flavorText' => clienttranslate(
        'For the first time in centuries, the Solstice Gate has opened. For the first time in ages, humanity will discover what lies beyond the gates.'
      ),
      'artist' => 'Jean-Baptiste Andrier',

      'costHand' => 5,
      'costReserve' => 5,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => STORMS,
        ]),
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => STORMS,
          'moreThan1' => true,
        ]),
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => STORMS,
          'moreThan1' => true,
        ]),
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => STORMS,
          'moreThan1' => true,
        ])
      ),
    ];
  }
}
