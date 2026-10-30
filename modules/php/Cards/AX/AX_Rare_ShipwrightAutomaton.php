<?php

namespace ALT\Cards\AX;

class AX_Rare_ShipwrightAutomaton extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_139_R1',
      'asset' => 'ALT_FUGUE_B_AX_139_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Shipwright Automaton'),
      'typeline' => clienttranslate('Character - Robot, Construction'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('"I\'ve reprogrammed all my Automata to assign them to the construction of the Homer." - Sierra'),
      'artist' => "Ba Vo",
      'extension' => 'NEJ',
      'subtypes' => [ROBOT, CONSTRUCTION],
      'costHand' => 4,
      'costReserve' => 4,
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 4,
      'changedStats' => ['forest', 'mountain', 'ocean'],
    ];
  }
}
