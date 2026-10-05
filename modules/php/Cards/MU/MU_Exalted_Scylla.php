<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Exalted_Scylla extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_142_E',
      'asset' => 'ALT_FUGUE_B_MU_142_E',
      'faction' => FACTION_MU,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Scylla'),
      'typeline' => clienttranslate('Character - Leviathan'),
      'artist' => "Taras Susak",
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [LEVIATHAN],
      'effectDesc' => clienttranslate('$<GIGANTIC>.  {H} Each player discards their hand, then draws three cards. Then, if the number of cards discarded this way is:  • 4+, I gain 1 boost.   • 6+, <SABOTAGE>.  • 8+, Target Expedition moves backward one region.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 7,
      'costReserve' => 5,
      'gigantic' => true,
      'effectHand' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'scylla']),
    ];
  }
}
