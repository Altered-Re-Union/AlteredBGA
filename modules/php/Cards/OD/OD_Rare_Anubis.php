<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_Anubis extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_20_R1',
      'asset' => 'ALT_CORE_B_OR_20_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Anubis'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Only a heart as light as a feather can stand up to the judgment of Anubis.'),
      'artist' => 'Romain Kurdi',
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('{J} Each player sacrifices a Character.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['mountain', 'costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'AllPlayersSacrifice1']),
    ];
  }
}
