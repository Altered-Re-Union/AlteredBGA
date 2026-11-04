<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_FaneofAeolus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_144_C',
      'asset' => 'ALT_FUGUE_B_AX_144_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Fane of Aeolus'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Anh Tung',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('{J} I gain 3 Kelon counters.  {T}, Spend 1 of my Kelon counters: Ready a Mana Orb.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'gainCounter', 'args' => ['counter' => 3, 'counterName' => clienttranslate('Kelon counter')]],
      ],
      'effectTap' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasCounterOnCard',
        'effect' => FT::SEQ(
          FT::ACTION(USE_COUNTER, ['consume' => 1], ['sourceId' => $this->id]),
          FT::ACTION(READY, ['cardId' => MANA])
        )
      ]),
    ];
  }
}
