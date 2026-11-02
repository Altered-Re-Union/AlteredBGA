<?php

namespace ALT\Cards\AX;

class AX_Rare_FoundryEngineer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_05_R1',
      'asset' => 'ALT_CORE_B_AX_05_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Foundry Engineer'),
      'typeline' => clienttranslate('Character - Engineer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Behind every Axiom masterwork, there are Foundry Engineers.'),
      'artist' => 'Damian Audino',
      'subtypes' => [ENGINEER],
      'effectDesc' => clienttranslate('{R} The next Permanent you play this Afternoon costs #{2}# less.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectReserve' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 2, 'permanent' => true]],
      ],
    ];
  }
}
