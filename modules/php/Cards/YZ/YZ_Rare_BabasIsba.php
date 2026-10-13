<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_BabasIsba extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_28_R1',
      'asset' => 'ALT_CORE_B_YZ_28_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => "Baba's Isba",
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate('Boney Legs, she\'s called, and her hut is one reason why.'),
      'artist' => 'Taras Susak',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate('{J} Draw a card.  {T}, Sacrifice a Character: $<AFTER_YOU>.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(DRAW, ['players' => ME]),
      'effectTap' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'canSacrifice',
        'effect' => FT::SEQ(
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER, TOKEN],
            'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
          ]),
          FT::ACTION(AFTER_YOU, [])
        ),
      ]),
    ];
  }
}
