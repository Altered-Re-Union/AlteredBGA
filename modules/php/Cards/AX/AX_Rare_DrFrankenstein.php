<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_DrFrankenstein extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_17_R1',
      'asset' => 'ALT_CORE_B_AX_17_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Dr. Frankenstein'),
      'typeline' => clienttranslate('Character - Engineer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('We sometimes seek companionship in the strangest of life forms.'),
      'artist' => 'Taras Susak',
      'subtypes' => [ENGINEER],
      'effectDesc' => clienttranslate('{R} You may activate the {j} abilities of target Permanent you control.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 2,
      'changedStats' => ['costReserve'],
      'effectReserve' => FT::ACTION(TARGET, [
        'targetType' => [PERMANENT],
        'targetPlayer' => ME,
        'hasEffects' => ['Played'],
        'upTo' => true,
        'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
      ]),
    ];
  }
}
