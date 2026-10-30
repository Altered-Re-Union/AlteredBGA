<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_FaneofAeolus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_144_R2',
      'asset' => 'ALT_FUGUE_B_AX_144_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fane of Aeolus'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Anh Tung',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('{J} I gain #4# Kelon counters.  {T}, Spend 1 of my Kelon counters: Ready a Mana Orb.  #When I\'m sacrificed — Sabotage.#'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'gainCounter', 'args' => ['counter' => 4, 'counterName' => clienttranslate('Kelon counter')]],
      ],
      'effectTap' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasCounterOnCard',
        'effect' => FT::SEQ(
          FT::ACTION(USE_COUNTER, ['consume' => 1], ['sourceId' => $this->id]),
          FT::ACTION(READY, ['cardId' => MANA])
        )
      ]),
      'effectPassive' => [
        'Discard' => [
          'condition' => 'isSacrificed',
          'output' => FT::ACTION(TARGET, [
              'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
              'targetLocation' => [RESERVE],
              'upTo' => true,
              'effect' => FT::ACTION(DISCARD, [])
          ]),
        ]
      ],
    ];
  }
}
