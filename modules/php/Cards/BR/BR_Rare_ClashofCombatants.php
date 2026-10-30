<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_ClashofCombatants extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_145_R1',
      'asset' => 'ALT_FUGUE_B_BR_145_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Clash of Combatants'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Fahmi Fauzi',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  #Play me for {1} less if you control a Character with Base Cost {4} or more.#  Send to Reserve target Character with Base Cost #{4} or less#.'),
      'flavorText' => clienttranslate('When will the cycle of hatred end ?'),
      'costHand' => 3,
      'costReserve' => 3,
      'dynamicCostReduction' => '1:hasControlCharacterWithMinBaseCost:4',
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 4,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ),
    ];
  }
}
