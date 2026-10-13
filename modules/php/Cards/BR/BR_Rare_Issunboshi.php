<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_Issunboshi extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_05_R1',
      'asset' => 'ALT_CORE_B_BR_05_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Issun-bōshi'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Small stature, big heart, immense adventures.'),
      'artist' => 'Anh Tung',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('#{R} Target Character gains 1 boost.#'),
      'supportDesc' => clienttranslate(
        '{D} : The next Character you play this turn gains 1 boost. (Discard me from Reserve to do this.)'
      ),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 2,
      'costHand' => 1,
      'costReserve' => 2,
      'changedStats' => ['costReserve'],
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains1Boost']),
      'effectReserve' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
    ];
  }
}
