<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_FaneofCirce extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_147_R2',
      'asset' => 'ALT_FUGUE_B_AX_147_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fane of Circe'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Jean-Baptiste Andrier',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('When you play a Character with Base Cost {2} or more — You may exhaust me to give it 1 boost.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['notTapped', 'isCardPlayed:character', 'cardPlayedCostCheck:2'],
          'output' => FT::SEQ_OPTIONAL(
            FT::ACTION(TAP, []),
            FT::GAIN(EFFECT, BOOST)
          )
        ],
      ]
    ];
  }
}
