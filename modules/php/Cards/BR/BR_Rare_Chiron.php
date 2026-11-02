<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_Chiron extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_11_R1',
      'asset' => 'ALT_CORE_B_BR_11_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Chiron'),
      'typeline' => clienttranslate('Character - Trainer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"And now, pray, mark all these things well in a wise heart."'),
      'artist' => 'Justice Wong',
      'subtypes' => [TRAINER],
      'effectDesc' => clienttranslate('{J} #Up to two target Characters# each gain 1 boost.'),
      'forest' => 1,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain', 'ocean'],
      'effectPlayed' => FT::ACTION(TARGET, [
        'upTo' => true,
        'n' => 2,
        'effect' => FT::ACTION(GAIN, ['type' => BOOST]),
      ]),
    ];
  }
}
