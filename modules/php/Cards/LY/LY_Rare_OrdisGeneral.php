<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_OrdisGeneral extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_136_R2',
      'asset' => 'ALT_FUGUE_B_OR_136_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ordis General'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{J} Target #<COMPANION># in play or in Reserve gains 1 boost.'),
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain', 'ocean'],
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, TOKEN],
        'subType' => COMPANION,
        'targetLocation' => [...STORMS, RESERVE],
        'effect' => FT::GAIN(TARGET, BOOST, 1),
      ]),
    ];
  }
}
