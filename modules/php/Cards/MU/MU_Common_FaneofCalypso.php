<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_FaneofCalypso extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_146_C',
      'asset' => 'ALT_FUGUE_B_MU_146_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Fane of Calypso'),
      'typeline' => clienttranslate('Landmark Permanent - Site'),
      'type' => PERMANENT,
      'artist' => 'Giovanni Calore',
      'extension' => 'NEJ',
      'subtypes' => [SITE, LANDMARK],
      'effectDesc' => clienttranslate('{J} Draw a card.  If an $<ANCHORED> Character would leave your Expeditions during the Afternoon, you may sacrifice me to have it lose Anchored instead.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(DRAW, ['players' => ME]),
      'sacrificeProtectAnchored' => true,
    ];
  }
}
