<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_KelonCylinder extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_26_R1',
      'asset' => 'ALT_CORE_B_AX_26_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kelon Cylinder'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate(
        'This little battery is Axiom\'s trump card. The Kelon produces phenomenal energy for which engineers find new applications every day.'
      ),
      'artist' => 'Anh Tung',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate(
        '#{J} Target Character gains 1 boost.#  {T} : I gain two Kelon counters.  {T}, Spend one of my Kelon counters: #target Character gains 1 boost.#'
      ),
      'costHand' => 1,
      'costReserve' => 1,
      'effectPlayed' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])]),

      'effectTap' => FT::XOR(
        FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'gainCounter',
          'args' => ['counter' => 2, 'counterName' => clienttranslate('Kelon counters')],
        ]),
        FT::SEQ(
          FT::ACTION(USE_COUNTER, ['consume' => 1], ['sourceId' => $this->id]),
          FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])])
        )
      ),
    ];
  }
}
