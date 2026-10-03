<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_FaneofNausicaa extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_146_C',
      'asset' => 'ALT_FUGUE_B_LY_146_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Fane of Nausicaa'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Jean-Baptiste Andrier',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('At Noon — If you\'re first player, $<RESUPPLY>.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPassive' => [
        'Noon' => [
          'condition' => 'isMe',
          'output' => FT::ACTION(CHECK_CONDITION, [
            'condition' => 'isFirstPlayer', 
            'effect' => FT::ACTION(RESUPPLY, [])
          ]),
        ]
      ]
    ];
  }
}
