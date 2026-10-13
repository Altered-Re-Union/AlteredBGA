<?php

namespace ALT\Cards\YZ;

class YZ_Rare_StudiousDisciple extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_04_R1',
      'asset' => 'ALT_CORE_B_YZ_04_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Studious Disciple'),
      'typeline' => clienttranslate('Character - Apprentice Mage'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('So far behind, and still so much to learn!'),
      'artist' => 'Zaël',
      'subtypes' => [APPRENTICE, MAGE],
      'effectDesc' => clienttranslate('#{R} The next Spell you play this Afternoon costs {1} less.#'),
      'supportDesc' => clienttranslate(
        '{D} : The next Spell you play this turn costs {1} less. (Discard me from Reserve to do this.)'
      ),
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
      'effectSupport' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => SPELL, 'reduction' => 1]],
      ],
      'supportIcon' => 'discard',
      'effectReserve' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => SPELL, 'reduction' => 1, 'permanent' => true]],
      ],
    ];
  }
}
