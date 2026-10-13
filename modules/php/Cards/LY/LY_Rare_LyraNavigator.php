<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_LyraNavigator extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_12_R1',
      'asset' => 'ALT_CORE_B_LY_12_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lyra Navigator'),
      'typeline' => clienttranslate('Character - Citizen'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'Like the Bravos, Lyras are drawn to freedom and the distant horizon.'
      ),
      'artist' => 'Taras Susak',
      'subtypes' => [CITIZEN],
      'supportDesc' => clienttranslate(
        '#{D} : <RESUPPLY>.# (Put the top card of your deck in Reserve. Discard me from Reserve to do this.)'
      ),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 4,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costReserve'],
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(RESUPPLY, []),
    ];
  }
}
