<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_KadigiranAlchemist extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_12_R1',
      'asset' => 'ALT_CORE_B_YZ_12_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kadigiran Alchemist'),
      'type' => CHARACTER,
      'subtypes' => [SCHOLAR],
      'effectDesc' => clienttranslate('{H} I gain #3 boosts$<BB>#.'),
      'typeline' => clienttranslate('Character - Scholar'),
      'flavorText' => clienttranslate(
        'Alchemy is not just a matter of turning lead to gold. It\'s about purifying yourself of imperfections to become your true self.'
      ),
      'artist' => 'Edward Cheekokseang',

      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 1,
      'effectHand' => FT::GAIN(ME, BOOST, 3),
    ];
  }
}
