<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_MunaDruid extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_13_R1',
      'asset' => 'ALT_CORE_B_MU_13_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Muna Druid'),
      'typeline' => clienttranslate('Character - Druid'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"We are the sentinels of the Skein, always keeping a finger on the pulse of nature."'),
      'artist' => 'Ba Vo',
      'subtypes' => [DRUID],
      'effectDesc' => clienttranslate('#{H} Up to one target Plant gains 2 boosts.#'),
      'supportDesc' => clienttranslate(
        '{D} : Target Character with Hand Cost {3} or less gains <ANCHORED>. (Discard me from Reserve to do this.)'
      ),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['forest', 'mountain', 'ocean'],
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'effect' => FT::GAIN(EFFECT, ANCHORED)]),
      'effectHand' => FT::ACTION(TARGET, [
        'upTo' => true,
        'subType' => PLANT,
        'effect' => FT::GAIN(EFFECT, BOOST, 2),
      ]),
    ];
  }
}
