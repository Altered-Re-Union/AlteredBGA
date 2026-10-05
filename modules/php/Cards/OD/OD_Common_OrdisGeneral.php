<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_OrdisGeneral extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_136_C',
      'asset' => 'ALT_FUGUE_B_OR_136_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Ordis General'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{J} Target Soldier in play or in Reserve gains 1 boost.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, TOKEN],
        'subType' => SOLDIER,
        'targetLocation' => [...STORMS, RESERVE],
        'effect' => FT::GAIN(TARGET, BOOST, 1),
      ]),
    ];
  }
}
