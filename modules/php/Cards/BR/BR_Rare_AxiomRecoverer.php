<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_AxiomRecoverer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_133_R2',
      'asset' => 'ALT_FUGUE_B_AX_133_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Axiom Recoverer'),
      'typeline' => clienttranslate('Character - Messenger, Soldier'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('The crash was violent, and the damage was considerable.'),
      'artist' => 'Anh Tung',
      'extension' => 'NEJ',
      'subtypes' => [MESSENGER, SOLDIER],
      'effectDesc' => clienttranslate('#{R}# $<RESUPPLY>.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['forest'],
      'effectReserve' => FT::ACTION(RESUPPLY, []),
    ];
  }
}
