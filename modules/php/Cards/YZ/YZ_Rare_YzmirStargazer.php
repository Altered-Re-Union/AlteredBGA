<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_YzmirStargazer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_08_R1',
      'asset' => 'ALT_CORE_B_YZ_08_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Yzmir Stargazer'),
      'typeline' => clienttranslate('Character - Scholar Mage'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('She won\'t read the future in the stars, she\'ll change it herself.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [SCHOLAR, MAGE],
      'effectDesc' => clienttranslate('#When I\'m sacrificed — You may have target Character gain 2 boosts.#'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['forest', 'ocean', 'costReserve'],
      'effectPassive' => [
        'Discard' => [
          'condition' => 'isSacrificed',
          'output' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 2]), 'upTo' => true]),
        ],
      ],
    ];
  }
}
