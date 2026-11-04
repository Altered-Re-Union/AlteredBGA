<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_FaneofCirce extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_147_C',
      'asset' => 'ALT_FUGUE_B_AX_147_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Fane of Circe'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Jean-Baptiste Andrier',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('{J} $<RESUPPLY>. (Put the top card of your deck in Reserve.)  When you play a Character with Base Cost {2} or more — You may exhaust me to give it 1 boost.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(RESUPPLY, []),
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