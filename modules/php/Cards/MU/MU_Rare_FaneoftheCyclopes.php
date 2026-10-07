<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_FaneoftheCyclopes extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_147_R2',
      'asset' => 'ALT_FUGUE_B_BR_147_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fane of the Cyclopes'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Zero Wen',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('When you play a Character with Base Cost {4} or more — You may exhaust me to create your Hero\'s Signature token in your Reserve.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['notTapped', 'isCardPlayed:character', 'cardPlayedCostCheck:4', 'hasHeroSignatureToken'],
          'output' => FT::SEQ_OPTIONAL(
            FT::ACTION(TAP, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => HERO_SIGNATURE,
              'targetLocation' => [RESERVE],
            ]),
          ),
        ],
      ],
    ];
  }
}
