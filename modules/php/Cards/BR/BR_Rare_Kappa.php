<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_Kappa extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_13_R1',
      'asset' => 'ALT_CORE_B_BR_13_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kappa'),
      'typeline' => clienttranslate('Character - Trainer Spirit'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Push through the pain. Giving up would hurt far more."'),
      'artist' => 'Matteo Spirito',
      'subtypes' => [TRAINER, SPIRIT],
      'effectDesc' => clienttranslate('#$<SEASONED>.#'),
      'supportDesc' => clienttranslate(
        '#{D} : The next Character you play this turn gains 1 boost.# (Discard me from Reserve to do this.)'
      ),
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 5,
      'costHand' => 4,
      'costReserve' => 4,
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains1Boost']),
      'seasoned' => true,
    ];
  }
}
