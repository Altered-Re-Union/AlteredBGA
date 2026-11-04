<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_PenelopeEternalWeaver extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_137_C',
      'asset' => 'ALT_FUGUE_B_AX_137_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Penelope, Eternal Weaver'),
      'typeline' => clienttranslate('Character - Noble'),
      'artist' => 'Damian Audino',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [NOBLE],
      'effectDesc' => clienttranslate('{H} Pay {1} less for the next Permanent you play this Afternoon, down to a minimum of {1}.  At Dusk — Sacrifice a Permanent with Base Cost {2} or less.'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(SPECIAL_EFFECT, 
        ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 1, 'minimum' => 1, 'permanent' => true]]
      ),
      'effectPassive' => [
        'Dusk' => [
          'output' => FT::ACTION(
            TARGET,
            [
              'targetPlayer' => ME,
              'targetType' => [PERMANENT],
              'upTo' => true,
              'minCost' => 2,
              'effect' =>
              FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            ]
          ),
        ],
      ],
    ];
  }
}
