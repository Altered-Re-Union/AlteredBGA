<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_Troy extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_139_C',
      'asset' => 'ALT_FUGUE_B_OR_139_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Troy'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('When you create a token — You may exhaust me to create another one in the same place. (If you copy a token created in the Expedition zone, the copy is created in the same Expedition.)'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPassive' => [
        'InvokeToken' => [
          'listeningConditions' => ['isNotSource'],
          'conditions' => ['isMe', 'notTapped'],
          'output' => FT::SEQ_OPTIONAL(
            FT::ACTION(TAP, ['cardId' => ME]),
            FT::ACTION(SPECIAL_EFFECT, ['effect' => 'copyInvoke'])
          ),
        ],
      ],
    ];
  }
}
