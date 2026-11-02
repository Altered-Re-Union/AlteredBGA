<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_TheHatter extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_18_R1',
      'asset' => 'ALT_CORE_B_LY_18_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Hatter'),
      'typeline' => clienttranslate('Character - Citizen'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        '"You can draw water out of a water-well, so I should think you could draw treacle out of a treacle-well — eh, stupid ?"'
      ),
      'artist' => 'Zero Wen',
      'subtypes' => [CITIZEN],
      'supportDesc' => clienttranslate(
        '{D} : Target Character with Hand Cost {3} or less gains <ANCHORED>. (Discard me from Reserve to do this.)'
      ),
      'forest' => 5,
      'mountain' => 0,
      'ocean' => 5,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['mountain', 'ocean', 'costReserve'],
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'effect' => FT::GAIN(EFFECT, ANCHORED)]),
    ];
  }
}
