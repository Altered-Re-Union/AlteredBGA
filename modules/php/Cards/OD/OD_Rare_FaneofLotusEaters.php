<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_FaneofLotusEaters extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_146_R2',
      'asset' => 'ALT_FUGUE_B_AX_146_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fane of Lotus-Eaters'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('{J} Sabotage.  At Noon — #If you\'re first player#, target Character in your Reserve gains 1 boost.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
      'effectPassive' => [
        'Noon' => [
          'conditions' => ['isFirstPlayer'],
          'output' => FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER],
            'targetLocation' => [RESERVE],
            'effect' => FT::ACTION(GAIN, ['type' => BOOST]),
          ]),
        ],
      ],
    ];
  }
}
