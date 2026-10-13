<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_BravosVanguard extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_14_R1',
      'asset' => 'ALT_CORE_B_BR_14_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Bravos Vanguard'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        '"We will be the arrow that pierces the veil of the unknown, the torch that banishes the mists of ignorance!"'
      ),
      'artist' => 'Edward Cheekokseang',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{J} You may have another target Character lose <FLEETING_CHAR> #and gain 1 boost#.'),
      'forest' => 4,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(TARGET, [
        'upTo' => true,
        'excludeSelf' => true,
        'effect' => FT::SEQ(FT::LOOSE(EFFECT, FLEETING), FT::GAIN(EFFECT, BOOST)),
      ]),
    ];
  }
}
