<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_FaneofLotusEaters extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_146_C',
      'asset' => 'ALT_FUGUE_B_AX_146_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Fane of Lotus-Eaters'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('{J} Sabotage (Discard up to one card from a Reserve).  At Noon — Target Character in your Reserve gains 1 boost.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
      'effectPassive' => [
        'Noon' => [
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
