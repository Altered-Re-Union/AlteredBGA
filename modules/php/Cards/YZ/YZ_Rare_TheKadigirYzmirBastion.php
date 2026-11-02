<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_TheKadigirYzmirBastion extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_30_R1',
      'asset' => 'ALT_CORE_B_YZ_30_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Kadigir, Yzmir Bastion'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate(
        'What dark, phantasmagorical secrets hide within Asgartha\'s College of Magic? Only madmen and the Yzmirs know for sure...'
      ),
      'artist' => 'Nestor Papatriantafyllou',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate(
        '{T} : The next Spell you play this turn is free. #If you play it from your hand, it loses <FLEETING>.#'
      ),
      'costHand' => 8,
      'costReserve' => 8,
      'effectTap' => FT::SEQ(
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextSpellIsFree']),
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'removeFleetingIfSpellPlayedHand'])
      ),
    ];
  }
}
