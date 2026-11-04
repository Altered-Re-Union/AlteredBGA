<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_TheShipwreck extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_145_C',
      'asset' => 'ALT_FUGUE_B_AX_145_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('The Shipwreck'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Kevin Sidharta',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('{J} I gain 3 Kelon counters.  At Noon — You may spend 1 of my Kelon counters to $<RESUPPLY>.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'gainCounter', 'args' => ['counter' => 3, 'counterName' => clienttranslate('Kelon counter')]],
      ],
      'effectPassive' => [
        'Noon' => [
          'conditions' => ['isMe', 'hasCounterOnCard'],
          'output' =>  FT::SEQ_OPTIONAL_MANUAL(
            FT::ACTION(USE_COUNTER, ['consume' => 1], ['sourceId' => $this->id]),
            FT::ACTION(RESUPPLY, [])
          )
        ],
      ],
    ];
  } 
}
