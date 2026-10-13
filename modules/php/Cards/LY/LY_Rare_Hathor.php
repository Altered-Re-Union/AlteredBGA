<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_Hathor extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_07_R1',
      'asset' => 'ALT_CORE_B_LY_07_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Hathor'),
      'typeline' => clienttranslate('Character - Deity Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Dance is the language of the soul.'),
      'artist' => 'Taras Susak',
      'subtypes' => [DEITY, ARTIST],
      'supportDesc' => clienttranslate(
        '{D} : Return another card from your Reserve to your hand. (Discard me from Reserve to do this.)'
      ),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costReserve'],
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetLocation' => [RESERVE],
        'targetType' => [CHARACTER, SPELL, PERMANENT],
        'effect' => FT::RETURN_TO_HAND(),
      ]),
    ];
  }
}
