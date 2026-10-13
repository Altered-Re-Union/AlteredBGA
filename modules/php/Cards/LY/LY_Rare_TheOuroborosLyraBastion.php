<?php

namespace ALT\Cards\LY;

class LY_Rare_TheOuroborosLyraBastion extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_30_R1',
      'asset' => 'ALT_CORE_B_LY_30_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Ouroboros, Lyra Bastion'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate(
        'The Expeditionary Corps\' mobile outpost soars high, like a spinning wheel weaving the wind. '
      ),
      'artist' => 'Khoa Viet',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate(
        'If you would roll one or more dice, instead roll that many dice plus one and ignore the roll of your choice.'
      ),

      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['costHand', 'costReserve'],
      'addDice' => 1,
    ];
  }
}
