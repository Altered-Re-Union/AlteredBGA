<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_Parvati extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_18_R1',
      'asset' => 'ALT_CORE_B_MU_18_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Parvati'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate(
        '#{J}# Target Character gains <ANCHORED>. (During Rest, it doesn\'t go to Reserve and it loses Anchored.)'
      ),
      'flavorText' => clienttranslate('Only in harmony can the world thrive.'),
      'artist' => 'Nestor Papatriantafyllou',

      'forest' => 3,
      'mountain' => 0,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 3,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(TARGET, ['effect' => FT::GAIN(EFFECT, ANCHORED)]),
    ];
  }
}
