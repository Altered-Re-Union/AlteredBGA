<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Common_StigmaofHavoc extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_LY_115_C',
      'asset'  => 'ALT_EOLE_B_LY_115_C',
      'faction'  => FACTION_LY,
      'rarity'  => RARITY_COMMON,
      'name'  => clienttranslate("Stigma of Havoc"),
      'typeline' => clienttranslate("Character - Corruption"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('If "wolfing down" had a face…'),
      'artist' => "Khoa Viet",
      'extension' => 'ROC',
      'subtypes'  => [CORRUPTION],
      'effectDesc' => clienttranslate('{H} Each player discards their Reserve, <RESUPPLIES>, then <RESUPPLIES> again.'),
      'supportDesc' => clienttranslate('{D} : Create an <AEROLITH> token in target player\'s Landmarks. (Discard me from Reserve to do this.)'),
      'supportIcon' => 'discard',
      'forest' => 5,
      'mountain' => 5,
      'ocean' => 6,
      'costHand' => 6,
      'costReserve' => 5,
      'effectHand' => FT::SEQ(
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'discardAllReserve']),
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerResupply']),
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerResupply']),
      ),
      'effectSupport' =>  [
        'action' => INVOKE_TOKEN,
        'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK], 'allPlayers' => true],
      ],
    ];
  }
}
