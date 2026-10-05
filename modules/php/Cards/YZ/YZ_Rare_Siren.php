<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_Siren extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_140_R1',
      'asset' => 'ALT_FUGUE_B_YZ_140_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Siren'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('{H} Target non-Anchored Character #with Base Cost {3} or less# defects.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 2,
      'costHand' => 5,
      'costReserve' => 1,
      'changedStats' => ['costHand'],
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'targetLocation' => [...STORMS],
        'excludedStatuses' => [ANCHORED],
        'maxBaseCost' => 3,
        'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect']),
      ]),
    ];
  }
}
