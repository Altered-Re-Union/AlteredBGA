<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_BravosPathfinder extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_08_R1',
      'asset' => 'ALT_CORE_B_BR_08_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Bravos Pathfinder'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Stay on the path, they say. I make my own path!"'),
      'artist' => 'Edward Cheekokseang',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('#$<SEASONED_ME_FS>.#  #{H} You may pay {2} to have me gain 1 boost.#'),
      'forest' => 0,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'seasoned' => true,
      'effectHand' => FT::SEQ_OPTIONAL(FT::ACTION(PAY, ['pay' => 2]), FT::GAIN(ME, BOOST)),
    ];
  }
}
