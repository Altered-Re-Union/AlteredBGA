<?php

namespace ALT\Cards\YZ;

class YZ_Rare_MoonlightJellyfish extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_05_R1',
      'asset' => 'ALT_CORE_B_YZ_05_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Moonlight Jellyfish'),
      'typeline' => clienttranslate('Character - Spirit'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'Theoretically, transdifferentiation can go on indefinitely, effectively rendering the jellyfish biologically immortal... and squishy.'
      ),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [SPIRIT],
      'effectDesc' => clienttranslate(
        'When I\'m sacrificed, if I was not <FLEETING> — Put me in Reserve.  #When I\'m sacrificed, if was <FLEETING> — Draw a card.#'
      ),
      'forest' => 1,
      'mountain' => 0,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
      'sacrificeAndNotFleetingGoToReserve' => true,
      'sacrificeAndFleetingDraw' => true,
    ];
  }
}
