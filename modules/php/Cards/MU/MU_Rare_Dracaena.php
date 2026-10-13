<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_Dracaena extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_15_R1',
      'asset' => 'ALT_CORE_B_MU_15_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Dracaena'),
      'typeline' => clienttranslate('Character - Dragon Plant'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'They\'re sometimes seen dozing close to a pond, soaking in the water and the sunlight... '
      ),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [DRAGON, PLANT],
      'effectDesc' => clienttranslate('{J} I gain $<ANCHORED>.  At Noon — I gain #2 boosts#.'),
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['mountain', 'ocean'],
      'effectPlayed' => FT::GAIN(ME, ANCHORED),
      'effectPassive' => [
        'Noon' => [
          'condition' => 'isMe',
          'output' => FT::GAIN(ME, BOOST, 2),
        ],
      ],
    ];
  }
}
