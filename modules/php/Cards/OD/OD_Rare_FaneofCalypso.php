<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_FaneofCalypso extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_146_R2',
      'asset' => 'ALT_FUGUE_B_MU_146_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fane of Calypso'),
      'typeline' => clienttranslate('Landmark Permanent - Site'),
      'type' => PERMANENT,
      'artist' => 'Giovanni Calore',
      'extension' => 'NEJ',
      'subtypes' => [SITE, LANDMARK],
      'effectDesc' => clienttranslate('{J} Resupply.  If an $<ASLEEP> Character would leave your Expeditions during the Afternoon, you may #sacrifice me instead#.'),
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(RESUPPLY, []),
      'sacrificeProtectAsleep' => true,
    ];
  }
}
