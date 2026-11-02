<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_ThreeLittlePigs extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_12_R1',
      'asset' => 'ALT_CORE_B_AX_12_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Three Little Pigs'),
      'type' => CHARACTER,
      'subtypes' => [ENGINEER],
      'effectDesc' => clienttranslate(
        '{J} If you control two or more Landmarks, I gain #2 boosts$<BB>#. (Cards in Reserve are not controlled.)'
      ),
      // 'supportDesc' => clienttranslate(
      //   '#{D} : The next Permanent you play this turn costs {1} less.# (Discard me from your Reserve to activate this effect)'
      // ),
      // 'supportIcon' => 'discard',
      'flavorText' => clienttranslate('Together they can build more than just a stone house.'),
      'typeline' => clienttranslate('Character - Engineer'),
      'artist' => 'Anh Tung',

      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasControl:landmark:2',
        'effect' => FT::GAIN(ME, BOOST, 2),
      ]),
      // 'effectSupport' => [
      //   'action' => SPECIAL_EFFECT,
      //   'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 1]],
      // ],
    ];
  }
}
