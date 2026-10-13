<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_OrdisSpy extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_14_R1',
      'asset' => 'ALT_CORE_B_OR_14_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ordis Spy'),
      'typeline' => clienttranslate('Character - Citizen'),
      'type' => CHARACTER,
      'subtypes' => [CITIZEN],
      'effectDesc' => clienttranslate('{H} $<SABOTAGE>.  #{R} Create an <ORDIS_RECRUIT> Soldier token in my Expedition.#'),
      'flavorText' => clienttranslate('Stirred, but not shaken.'),
      'artist' => 'Matteo Spirito',

      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
      'effectReserve' => FT::ACTION(INVOKE_TOKEN, [
        'pId' => 'source',
        'tokenType' => 'OD_Common_OrdisRecruit',
        'targetLocation' => ['source'],
      ]),
    ];
  }
}
