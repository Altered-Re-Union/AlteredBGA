<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_Issitoq extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_19_R1',
      'asset' => 'ALT_CORE_B_OR_19_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Issitoq'),
      'typeline' => clienttranslate('Character - Deity Bureaucrat'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('No one can escape their gaze.'),
      'artist' => 'Atanas Lozanski',
      'subtypes' => [DEITY, BUREAUCRAT],
      'effectDesc' => clienttranslate('Your other Expedition and the Expedition facing it can\'t move forward.'),
      'supportDesc' => clienttranslate('{D} : Draw a card. (Discard me from Reserve to do this.)'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 5,
      'costReserve' => 5,
      'changedStats' => ['forest', 'mountain', 'ocean', 'costHand', 'costReserve'],
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(DRAW, ['players' => ME]),
      'oppositeDefender' => true,
    ];
  }
}
