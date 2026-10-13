<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_AxiomScrambler extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_15_R1',
      'asset' => 'ALT_CORE_B_AX_15_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Axiom Scrambler'),
      'typeline' => clienttranslate('Character - Engineer'),
      'type' => CHARACTER,
      'subtypes' => [ENGINEER],
      'flavorText' => clienttranslate(
        'Pushing back the Tumult sometimes prevents other Alterers from materializing their Eidolons. Regrettable, but necessary.'
      ),
      'effectDesc' => clienttranslate('{H} $<SABOTAGE>.'),
      'artist' => 'HuoMiao Studio',

      'forest' => 2,
      'mountain' => 4,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['costHand'],
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
    ];
  }
}
