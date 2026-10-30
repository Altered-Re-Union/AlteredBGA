<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_Troy extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_139_R2',
      'asset' => 'ALT_FUGUE_B_OR_139_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Troy'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('If you would create a token, create #two# in the same place instead.'),
      'costHand' => 7,
      'costReserve' => 7,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPassive' => [
        'InvokeToken' => [
          'listeningConditions' => ['isNotSource'],
          'conditions' => ['isMe'],
          'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'copyInvoke'])
        ],
      ],
    ];
  }
}
