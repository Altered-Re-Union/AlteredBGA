<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_StickyNoteSeals extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_25_R1',
      'asset' => 'ALT_CORE_B_OR_25_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Sticky Note Seals'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Some lessons stick better than others.'),
      'artist' => 'Atanas Lozanski',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate(
        'Choose one:  • Send to Reserve target Character with Hand Cost {4} or more.  • Discard target Permanent with Hand Cost {4} or more.'
      ),
      'costHand' => 3,
      'costReserve' => 4,
      'changedStats' => ['costReserve'],
      'effectPlayed' => [
        'optional' => true,
        'type' => NODE_XOR,
        'childs' => [
          FT::ACTION(TARGET, ['minHandCost' => 4, 'effect' => FT::DISCARD_TO_RESERVE()]),
          FT::ACTION(TARGET, ['minHandCost' => 4, 'targetType' => [PERMANENT], 'effect' => FT::ACTION(DISCARD, [])]),
        ],
      ],
    ];
  }
}
