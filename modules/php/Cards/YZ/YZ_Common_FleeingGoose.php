<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_FleeingGoose extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_131_C',
      'asset' => 'ALT_FUGUE_B_YZ_131_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Fleeing Goose'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'artist' => 'Zaeliven',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL],
      'effectDesc' => clienttranslate('When I\'m sacrificed — Target Character in your Expeditions gains 1 boost.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
      'effectPassive' => [
        'Discard' => [
          'condition' => 'isSacrificed',
          'output' => FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER],
            'targetLocation' => [...STORMS],
            'effect' => FT::ACTION(GAIN, [
              'type' => BOOST, 
              'n' => 1
            ]), 
          ]),
        ],
      ],
    ];
  }
}
