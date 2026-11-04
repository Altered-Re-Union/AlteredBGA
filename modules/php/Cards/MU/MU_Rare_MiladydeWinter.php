<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_MiladydeWinter extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_DUSTER_B_YZ_90_R2',
      'asset'  => 'ALT_DUSTER_B_YZ_90_R',

      'faction'  => FACTION_MU,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Milady de Winter"),
      'typeline' => clienttranslate("Character - Noble Rogue"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('There\'s something so delectable about nabbing someone else\'s assets.'),
      'artist' => "Zero Wen",
      'extension' => 'SDU',
      'subtypes'  => [NOBLE, ROGUE],
      'effectDesc' => clienttranslate('{H} Take control of target token Permanent, putting it in my Expedition if it\'s an Expedition Permanent.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(TARGET, [
        'targetLocation' => IN_PLAY,
        'targetType' => [PERMANENT],
        'onlyToken' => true,
        'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect', 'args' => ['moveToMe' => true, 'takeControl' => true]])
      ])
    ];
  }
}
