<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_HornofPlenty extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_143_C',
      'asset' => 'ALT_FUGUE_B_MU_143_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Horn of Plenty'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('Your Companions have: "{R} I gain 1 boost."'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['isCharacterFromReserveNotBlocked', 'isCardPlayed:companion', 'hasSameOwner'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
      ],
    ];
  }
}
