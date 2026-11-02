<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_Mowgli extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_07_R1',
      'asset' => 'ALT_CORE_B_MU_07_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Mowgli'),
      'typeline' => clienttranslate('Character - Druid'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Wake up, you lazy furball!"'),
      'artist' => 'Ba Vo',
      'subtypes' => [DRUID],
      'supportDesc' => clienttranslate(
        '#{D} : Target Character with Hand Cost {3} or less gains <ANCHORED>.# (Discard me from Reserve to do this.)'
      ),
      'forest' => 3,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 1,
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'effect' => FT::GAIN(EFFECT, ANCHORED)]),
    ];
  }
}
