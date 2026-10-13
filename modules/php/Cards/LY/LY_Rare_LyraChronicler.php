<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Rare_LyraChronicler extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_LY_16_R1',
      'asset' => 'ALT_CORE_B_LY_16_R',

      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lyra Chronicler'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'Eidolon or human, we are all shaped by stories. They\'re the building blocks of our identity. '
      ),
      'artist' => 'Taras Susak',
      'subtypes' => [ARTIST],
      'supportDesc' => clienttranslate(
        '#{D} : <RESUPPLY>.# (Put the top card of your deck in Reserve. Discard me from Reserve to do this.)'
      ),
      'forest' => 4,
      'mountain' => 0,
      'ocean' => 4,
      'costHand' => 3,
      'costReserve' => 3,
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(RESUPPLY, []),
    ];
  }
}
