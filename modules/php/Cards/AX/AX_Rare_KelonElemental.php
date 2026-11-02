<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_KelonElemental extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_04_R1',
      'asset' => 'ALT_CORE_B_AX_04_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kelon Elemental'),
      'typeline' => clienttranslate('Character - Elemental'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('In true Axiom fashion, Kelon Elementals like to put everything to the taste.'),
      'artist' => 'Zero Wen',
      'subtypes' => [ELEMENTAL],
      'effectDesc' => clienttranslate('{H} #You may# put a card from your hand in Reserve.'),
      'forest' => 1,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(
        TARGET,
        [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'effect' => FT::DISCARD_TO_RESERVE(),
          'upTo' => true,
        ],
        ['optional' => true]
      ),
    ];
  }
}
