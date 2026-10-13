<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_MechanicalTraining extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_22_R1',
      'asset' => 'ALT_CORE_B_AX_22_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Mechanical Training'),
      'typeline' => clienttranslate('Spell - Boon'),
      'type' => SPELL,
      'flavorText' => clienttranslate('To learn to create, first learn to fix.'),
      'artist' => 'Damian Audino',
      'subtypes' => [BOON],
      'effectDesc' => clienttranslate('Activate the {j} abilities of #up to two# target Permanents you control.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand'],
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [PERMANENT],
        'upTo' => true,
        'n' => 2,
        'targetPlayer' => ME,
        'hasEffects' => ['Played'],
        'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
      ]),
    ];
  }
}
