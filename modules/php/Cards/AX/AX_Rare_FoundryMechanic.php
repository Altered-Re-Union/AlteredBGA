<?php

namespace ALT\Cards\AX;

class AX_Rare_FoundryMechanic extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_07_R1',
      'asset' => 'ALT_CORE_B_AX_07_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Foundry Mechanic'),
      'typeline' => clienttranslate('Character - Engineer'),
      'type' => CHARACTER,
      'subtypes' => [ENGINEER],
      'flavorText' => clienttranslate('You can\'t choose when and where a quick fix will be needed.'),
      'supportDesc' => clienttranslate(
        '{D} : The next Permanent you play this turn costs {1} less. (Discard me from Reserve to do this.)'
      ),
      'artist' => 'Fahmi Fauzi',

      'supportIcon' => 'discard',
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 2,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['forest', 'ocean'],
      'effectSupport' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 1]],
      ],
    ];
  }
}
