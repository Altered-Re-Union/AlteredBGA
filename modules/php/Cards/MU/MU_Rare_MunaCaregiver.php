<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_MunaCaregiver extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_04_R1',
      'asset' => 'ALT_CORE_B_MU_04_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Muna Caregiver'),
      'typeline' => clienttranslate('Character - Druid'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"We don’t inherit the earth from our ancestors, we borrow it from our children."'),
      'artist' => 'Nestor Papatriantafyllou',
      'subtypes' => [DRUID],
      'supportDesc' => clienttranslate(
        '{D} : Target Character with Hand Cost {3} or less gains <ANCHORED>. (Discard me from Reserve to do this.)'
      ),
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['forest', 'mountain'],
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'effect' => FT::GAIN(EFFECT, ANCHORED)]),
    ];
  }
}
