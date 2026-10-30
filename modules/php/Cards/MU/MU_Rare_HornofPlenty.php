<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_HornofPlenty extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_143_R1',
      'asset' => 'ALT_FUGUE_B_MU_143_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Horn of Plenty'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('#{J} Resupply.#  Your Companions have: "{R} I gain 1 boost."'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(RESUPPLY, []),
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['isCharacterFromReserveNotBlocked', 'isCardPlayed:companion', 'hasSameOwner'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
      ],
    ];
  }
}
