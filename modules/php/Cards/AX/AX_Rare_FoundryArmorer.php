<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_FoundryArmorer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_16_R1',
      'asset' => 'ALT_CORE_B_AX_16_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Foundry Armorer'),
      'typeline' => clienttranslate('Character - Engineer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('No Brassbug would survive in the Tumult without armor.'),
      'artist' => 'Anh Tung',
      'subtypes' => [ENGINEER],
      'effectDesc' => clienttranslate('#{J}# Create a <BRASSBUG> Robot token in target Expedition.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(INVOKE_TOKEN, [
        'pId' => 'source',
        'tokenType' => 'AX_Common_Brassbug',
        'targetLocation' => STORMS,
      ]),
    ];
  }
}
