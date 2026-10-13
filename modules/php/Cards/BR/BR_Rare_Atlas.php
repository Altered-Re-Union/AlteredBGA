<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_Atlas extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_20_R1',
      'asset' => 'ALT_CORE_B_BR_20_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Atlas'),
      'typeline' => clienttranslate('Character - Titan'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Not even the weight of the sky could make him buckle.'),
      'artist' => 'Matteo Spirito',
      'subtypes' => [TITAN],
      'effectDesc' => clienttranslate('$<GIGANTIC>.  #$<SEASONED>.#'),
      'supportDesc' => clienttranslate(
        '#{D} : The next Character you play this turn gains 2 boosts.# (Discard me from Reserve to do this.)'
      ),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 5,
      'costReserve' => 5,
      'gigantic' => true,
      'seasoned' => true,
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains2Boost']),
    ];
  }
}
