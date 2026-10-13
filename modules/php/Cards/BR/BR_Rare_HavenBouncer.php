<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_HavenBouncer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_15_R1',
      'asset' => 'ALT_CORE_B_BR_15_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Haven Bouncer'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Only the bravest can enter Haven.'),
      'artist' => 'Edward Cheekokseang',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{H} $<SABOTAGE>.  {R} I gain #2 boosts#.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
      'effectReserve' => FT::GAIN(ME, BOOST, 2),
    ];
  }
}
