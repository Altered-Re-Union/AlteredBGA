<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_KuwattheDissenter extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_15_R1',
      'asset' => 'ALT_CORE_B_YZ_15_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kuwat, the Dissenter'),
      'typeline' => clienttranslate('Character - Mage'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        '"There are gates one should not open, there are seals one should not breach. And yet I will."'
      ),
      'artist' => 'Ba Vo',
      'subtypes' => [MAGE],
      'effectDesc' => clienttranslate('{J} Sacrifice a Character.'),
      'forest' => 5,
      'mountain' => 5,
      'ocean' => 5,
      'costHand' => 4,
      'costReserve' => 3,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER, TOKEN],
        'targetLocation' => STORMS,
        'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
      ]),
    ];
  }
}
