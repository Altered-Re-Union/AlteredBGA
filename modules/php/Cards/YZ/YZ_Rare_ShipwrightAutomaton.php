<?php
namespace ALT\Cards\YZ;

class YZ_Rare_ShipwrightAutomaton extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_139_R2',
      'asset' => 'ALT_FUGUE_B_AX_139_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Shipwright Automaton'),
      'typeline' => clienttranslate('Character - Robot, Construction'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('"I\'ve reprogrammed all my Automata to assign them to the construction of the Homer." - Sierra'),
      'artist' => "Ba Vo",
      'extension' => 'NEJ',
      'subtypes' => [ROBOT, CONSTRUCTION],
      'effectDesc' => clienttranslate('#I\'m also considered a Spell, even when not in play.# (Play me as a Character, but it counts as playing a Spell for other abilities.)'),
      'costHand' => 4,
      'costReserve' => 4,
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 4,
      'changedStats' => ['forest', 'mountain', 'ocean'],
      'additionalType' => [SPELL]
    ];
  }
}
