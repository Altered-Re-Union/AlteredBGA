<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_BanishingGate extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_24_R1',
      'asset' => 'ALT_CORE_B_YZ_24_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Banishing Gate'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('Discard target Character or Permanent.'),
      'flavorText' => clienttranslate('Let me walk you to the door.'),
      'artist' => 'Jean-Baptiste Andrier',

      'costHand' => 4,
      'costReserve' => 5,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN, PERMANENT], 'effect' => FT::ACTION(DISCARD, [])]),
    ];
  }
}
