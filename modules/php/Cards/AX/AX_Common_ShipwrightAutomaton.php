<?php

namespace ALT\Cards\AX;

class AX_Common_ShipwrightAutomaton extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_139_C',
      'asset' => 'ALT_FUGUE_B_AX_139_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Shipwright Automaton'),
      'typeline' => clienttranslate('Character - Robot, Construction'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('"I\'ve reprogrammed all my Automata to assign them to the construction of the Homer." - Sierra'),
      'artist' => "Ba Vo",
      'extension' => 'NEJ',
      'subtypes' => [ROBOT, CONSTRUCTION],
      'costHand' => 4,
      'costReserve' => 4,
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
    ];
  }
}
