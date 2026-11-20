<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_Theseus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_BISE_B_BR_58_R1',
      'asset'  => 'ALT_BISE_B_BR_58_R',

      'faction'  => FACTION_BR,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Theseus"),
      'typeline' => clienttranslate("Character - Adventurer"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('For the brave souls exploring the labyrinthine depths of the city, life often hangs by the thinnest of threads.'),
      'artist' => "Ed Chee, S.Yong & Stephen",
      'extension' => 'WFTM',
      'subtypes'  => [ADVENTURER],
      'effectDesc' => clienttranslate('#$<SCOUT_4> {4}.#  {J} Send target Character #to Reserve.#  {R} I gain #3 boosts.#'),
      'forest' => 6,
      'mountain' => 6,
      'ocean' => 6,
      'costHand' => 8,
      'costReserve' => 8,
      'scout' => 4,
      'effectPlayed' =>  FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'effect' => FT::DISCARD_TO_RESERVE()]),
      'effectReserve' => FT::GAIN(ME, BOOST, 3, 99, true)
    ];
  }
}
