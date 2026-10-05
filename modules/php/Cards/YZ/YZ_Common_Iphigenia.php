<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_Iphigenia extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_143_C',
      'asset' => 'ALT_FUGUE_B_YZ_143_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Iphigenia'),
      'typeline' => clienttranslate('Character - Citizen, Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('In her death, the Greek fleet found their way to Troy.'),
      'artist' => 'Leena Sooba',
      'extension' => 'NEJ',
      'subtypes' => [CITIZEN, NOBLE],
      'effectDesc' => clienttranslate('When I\'m sacrificed — Draw a card.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPassive' => [
        'Discard' => [
          'condition' => 'isSacrificed',
          'output' => FT::ACTION(DRAW, ['players' => ME], ['pId' => 'owner']),
        ],
      ],
    ];
  }
}
