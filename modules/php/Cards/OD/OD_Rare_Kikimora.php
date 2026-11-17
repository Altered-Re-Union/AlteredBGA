<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_Kikimora extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_DUSTER_B_OR_95_R1',
      'asset'  => 'ALT_DUSTER_B_OR_95_R',

      'faction'  => FACTION_OD,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Kikimora"),
      'typeline' => clienttranslate("Character - Animal Spirit"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('Respect your household, and she will help you. But if you neglect it, beware!'),
      'artist' => "Matteo Spirito",
      'extension' => 'SDU',
      'subtypes'  => [ANIMAL, SPIRIT],
      'effectDesc' => clienttranslate('#When you play a Permanent — If I have no boosts, I gain 1 boost.#'),
      'supportDesc' => clienttranslate('{D} : Pay {1} less for the next Permanent you play this turn, down to a minimum of {1}.'),
      'supportIcon' => 'discard',
      'forest' => 1,
      'mountain' => 0,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['ocean'],
      'effectSupport' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 1, 'minimum' => 1, 'permanent' => false]],
      ],
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['notTapped', 'isCardPlayed:permanent', 'hasNoBoost', 'isInStorms'],
          'output' => FT::GAIN(ME, BOOST)
        ],
      ],
    ];
  }
}
