<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_Charge extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_23_R1',
      'asset' => 'ALT_CORE_B_OR_23_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Charge!'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'flavorText' => clienttranslate(
        'Facing terrible odds and an unfathomably huge Leviathan, the Ordis legion charged nonetheless.'
      ),
      'artist' => 'Zero Wen',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('Characters you control gain 1 boost.'),
      'costHand' => 2,
      'costReserve' => 3,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(TARGET, ['targetPlayer' => ME, 'n' => INFTY, 'effect' => FT::GAIN(EFFECT, BOOST)]),
    ];
  }
}
