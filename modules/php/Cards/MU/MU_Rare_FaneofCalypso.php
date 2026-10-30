<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_FaneofCalypso extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_146_R1',
      'asset' => 'ALT_FUGUE_B_MU_146_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fane of Calypso'),
      'typeline' => clienttranslate('Landmark Permanent - Site'),
      'type' => PERMANENT,
      'artist' => 'Giovanni Calore',
      'extension' => 'NEJ',
      'subtypes' => [SITE, LANDMARK],
      'effectDesc' => clienttranslate('{J} Resupply.  If an $<ANCHORED> Character would leave your Expeditions during the Afternoon, you may sacrifice me to have it lose Anchored instead.'),
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(RESUPPLY, []),
      'sacrificeProtectAnchored' => true,
    ];
  }
}
