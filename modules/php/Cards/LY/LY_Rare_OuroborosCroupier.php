<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_OuroborosCroupier extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_17_R1',
      'asset' => 'ALT_CORE_B_LY_17_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ouroboros Croupier'),
      'typeline' => clienttranslate('Character - Citizen'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('The house never loses.'),
      'artist' => 'Anh Tung',
      'subtypes' => [CITIZEN],
      'effectDesc' => clienttranslate('{H} Roll a die. On a 4+, draw a card. On a 1-3, $<RESUPPLY_LOW>.'),
      'supportDesc' => clienttranslate(
        '#{D} : The next card you play this turn costs {1} less.# (Discard me from Reserve to do this.)'
      ),
      'forest' => 0,
      'mountain' => 5,
      'ocean' => 5,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['mountain', 'ocean'],
      'effectHand' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '1-3' => FT::ACTION(RESUPPLY, []),
          '4+' => FT::ACTION(DRAW, ['players' => ME]),
        ],
      ]),
      'supportIcon' => 'discard',
      'effectSupport' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => ALL, 'reduction' => 1]],
      ],
    ];
  }
}
