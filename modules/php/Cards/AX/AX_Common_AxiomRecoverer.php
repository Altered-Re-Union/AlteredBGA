<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_AxiomRecoverer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_133_C',
      'asset' => 'ALT_FUGUE_B_AX_133_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Axiom Recoverer'),
      'typeline' => clienttranslate('Character - Messenger, Soldier'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('The crash was violent, and the damage was considerable.'),
      'artist' => 'Anh Tung',
      'extension' => 'NEJ',
      'subtypes' => [MESSENGER, SOLDIER],
      'effectDesc' => clienttranslate('{H} $<RESUPPLY>. (Put the top card of your deck in Reserve.)'),
      'forest' => 0,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(RESUPPLY, []),
    ];
  }
}
