<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_OffYouGo extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_21_R1',
      'asset' => 'ALT_CORE_B_YZ_21_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Off You Go!'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('Time to kiss Kansas goodbye.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('Send to Reserve target Character with Hand Cost #{5} or less#.'),
      'costHand' => 3,
      'costReserve' => 4,
      'changedStats' => ['costHand'],
      'effectPlayed' => FT::ACTION(TARGET, ['maxHandCost' => 5, 'effect' => FT::DISCARD_TO_RESERVE()]),
    ];
  }
}
