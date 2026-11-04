<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_ClashofCombatants extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_145_C',
      'asset' => 'ALT_FUGUE_B_BR_145_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Clash of Combatants'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Fahmi Fauzi',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Send to Reserve target Character with Base Cost {5} or less.'),
      'flavorText' => clienttranslate('When will the cycle of hatred end ?'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 5,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ),
    ];
  }
}
