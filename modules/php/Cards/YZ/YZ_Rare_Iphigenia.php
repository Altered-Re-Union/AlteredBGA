<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_Iphigenia extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_143_R1',
      'asset' => 'ALT_FUGUE_B_YZ_143_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Iphigenia'),
      'typeline' => clienttranslate('Character - Citizen, Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('In her death, the Greek fleet found their way to Troy.'),
      'artist' => 'Leena Sooba',
      'extension' => 'NEJ',
      'subtypes' => [CITIZEN, NOBLE],
      'effectDesc' => clienttranslate('When I\'m sacrificed — #Put me in my owner\'s Mana zone# (as an exhausted Mana Orb).'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['costHand'],
      'effectPassive' => [
        'Discard' => [
          'condition' => 'isSacrificed',
          'output' => FT::ACTION(DISCARD, [
            'cardId' => ME, 
            'destination' => MANA, 
            'tapped' => true,
            'force' => true
          ]),
        ],
      ],
    ];
  }
}
