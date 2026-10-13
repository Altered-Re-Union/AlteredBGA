<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_HavenTrainee extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_09_R1',
      'asset' => 'ALT_CORE_B_BR_09_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Haven Trainee'),
      'typeline' => clienttranslate('Character - Apprentice'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"All right, lad, show me what you’ve learned."'),
      'artist' => 'Atanas Lozanski',
      'subtypes' => [APPRENTICE],
      'effectDesc' => clienttranslate('{R} I gain 2 boosts.'),
      'supportDesc' => clienttranslate(
        '#{D} : The next Character you play this turn gains 1 boost.# (Discard me from Reserve to do this.)'
      ),
      'forest' => 3,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 4,
      'effectReserve' => FT::GAIN(ME, BOOST, 2),
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains1Boost']),
    ];
  }
}
