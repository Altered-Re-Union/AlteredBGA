<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_Thoth extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_22_R1',
      'asset' => 'ALT_CORE_B_OR_22_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Thoth'),
      'typeline' => clienttranslate('Character - Deity Bureaucrat'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"The pen is mightier than the sword."'),
      'artist' => 'Christophe Young',
      'subtypes' => [DEITY, BUREAUCRAT],
      'effectDesc' => clienttranslate(
        'When my Expedition fails to move forward during Dusk — Create #two# <ORDIS_RECRUIT> Soldier tokens in target Expedition after Rest.'
      ),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPassive' => [
        'AfterDusk' => [
          'condition' => 'myExpeditionHasNotMoved',
          'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'AfterRest2OrdisRecruit']),
        ],
      ],
    ];
  }
}
