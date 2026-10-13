<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_HavenWarrior extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_17_R1',
      'asset' => 'ALT_CORE_B_BR_17_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Haven Warrior'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'subtypes' => [SOLDIER],
      'supportDesc' => clienttranslate(
        '#{D} : The next Character you play this turn gains 1 boost.# (Discard me from Reserve to do this.)'
      ),
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains1Boost']),
      'flavorText' => clienttranslate('We\'ve all lived through some things. But she\'s been through worse.'),
      'artist' => 'Edward Cheekokseang',

      'forest' => 4,
      'mountain' => 4,
      'ocean' => 4,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['mountain'],
    ];
  }
}
