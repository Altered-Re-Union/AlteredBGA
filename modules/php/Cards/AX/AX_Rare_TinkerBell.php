<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_TinkerBell extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_09_R1',
      'asset' => 'ALT_CORE_B_AX_09_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Tinker Bell'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Ting! Ting-a-ling! There\'s something magical in mischievous tinkering...'),
      'artist' => 'Anh Tung',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('{R} $<SABOTAGE>.'),
      'forest' => 1,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 3,
      'changedStats' => ['costHand'],
      'effectReserve' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
    ];
  }
}
