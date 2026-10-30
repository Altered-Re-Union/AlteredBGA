<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_Iphigenia extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_143_R2',
      'asset' => 'ALT_FUGUE_B_YZ_143_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Iphigenia'),
      'typeline' => clienttranslate('Character - Citizen, Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('In her death, the Greek fleet found their way to Troy.'),
      'artist' => 'Leena Sooba',
      'extension' => 'NEJ',
      'subtypes' => [CITIZEN, NOBLE],
      'effectDesc' => clienttranslate('When #you sacrifice a Permanent# — #Put me in my owner\'s Mana zone# (as an exhausted Mana Orb).'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['costHand'],
      'effectPassive' => [
        'Discard' => [
          'conditions' => ['isMe', 'isSacrifice:permanent'],
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
