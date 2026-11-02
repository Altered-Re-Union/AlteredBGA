<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_OuroborosInkcaster extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_10_R1',
      'asset' => 'ALT_CORE_B_LY_10_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ouroboros Inkcaster'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('When luck joins in the game, cleverness scores double. '),
      'artist' => 'Khoa Viet',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate(
        'When I go to Reserve from the Expedition zone — You may return another card from your Reserve to your hand.'
      ),
      'supportDesc' => clienttranslate(
        '#{D} : The next card you play this turn costs {1} less.# (Discard me from Reserve to do this.)'
      ),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['forest', 'mountain', 'costHand', 'costReserve'],
      'supportIcon' => 'discard',
      'effectSupport' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => ALL, 'reduction' => 1]],
      ],
      'effectPassive' => [
        'LeaveExpedition' => [
          'condition' => 'isToReserve',
          'output' => FT::ACTION(
            TARGET,
            [
              'targetLocation' => [RESERVE],
              'targetPlayer' => ME,
              'targetType' => [CHARACTER, TOKEN, PERMANENT, SPELL],
              'excludeSelf' => true,
              'effect' => FT::RETURN_TO_HAND(),
            ],
            ['optional' => true]
          ),
        ],
      ],
    ];
  }
}
