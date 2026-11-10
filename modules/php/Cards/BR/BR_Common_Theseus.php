<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Common_Theseus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_BISE_B_BR_58_C',
      'asset'  => 'ALT_BISE_B_BR_58_C',

      'faction'  => FACTION_BR,
      'rarity'  => RARITY_COMMON,
      'name'  => clienttranslate("Theseus"),
      'typeline' => clienttranslate("Character - Adventurer"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('For the brave souls exploring the labyrinthine depths of the city, life often hangs by the thinnest of threads.'),
      'artist' => "Ed Chee, S.Yong & Stephen",
      'extension' => 'WFTM',
      'subtypes'  => [ADVENTURER],
      'effectDesc' => clienttranslate('$<SCOUT_3> {3}.  {J} Return target Character to its owner\'s hand.  {R} I gain 2 boosts.'),
      'forest' => 6,
      'mountain' => 6,
      'ocean' => 6,
      'costHand' => 8,
      'costReserve' => 8,
      'scout' => 3,
      'effectPlayed' =>  FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'effect' => FT::RETURN_TO_HAND()]),
      'effectReserve' => FT::GAIN(ME, BOOST, 2, 99, true)
    ];
  }
}
