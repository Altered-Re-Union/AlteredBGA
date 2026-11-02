<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_OrdisAttorney extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_16_R1',
      'asset' => 'ALT_CORE_B_OR_16_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ordis Attorney'),
      'typeline' => clienttranslate('Character - Bureaucrat'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Objection!'),
      'artist' => 'Nestor Papatriantafyllou',
      'subtypes' => [BUREAUCRAT],
      'effectDesc' => clienttranslate('When my Expedition fails to move forward during Dusk — Draw a card.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain', 'ocean', 'costHand', 'costReserve'],
      'effectPassive' => [
        'AfterDusk' => [
          'condition' => 'myExpeditionHasNotMoved',
          'output' => FT::ACTION(DRAW, ['players' => ME]),
        ],
      ],
    ];
  }
}
