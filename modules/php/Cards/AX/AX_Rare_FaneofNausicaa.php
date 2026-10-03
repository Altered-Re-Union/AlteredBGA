<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_FaneofNausicaa extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_146_R2',
      'asset' => 'ALT_FUGUE_B_LY_146_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fane of Nausicaa'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Jean-Baptiste Andrier',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('#Pay {1} more to play cards from your hand if your Reserve isn\'t empty.#  #At Noon# — $<RESUPPLY>.'),
      'costHand' => 2,
      'costReserve' => 2,
      'dynamicIncreaseHandCost' => '1:isReserveNotEmpty',
      'effectPassive' => [
        'Noon' => [
          'condition' => 'isMe',
          'output' => FT::ACTION(RESUPPLY, [])
        ],
      ]
    ];
  }
}
