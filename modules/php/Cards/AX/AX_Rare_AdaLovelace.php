<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_AdaLovelace extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_13_R1',
      'asset' => 'ALT_CORE_B_AX_13_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ada Lovelace'),
      'typeline' => clienttranslate('Character - Engineer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        '"Imagination is the discovering faculty. It is that which penetrates the unseen worlds around us."'
      ),
      'artist' => 'Taras Susak',
      'subtypes' => [ENGINEER],
      'effectDesc' => clienttranslate('{R} You may put a card from your hand in Reserve #to draw a card#.'),
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain'],
      'effectReserve' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasCardsInHand',
        'effect' => FT::SEQ_OPTIONAL(
          FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            // 'upTo' => true,
            'effect' => FT::DISCARD_TO_RESERVE(),
          ]),
          FT::ACTION(DRAW, ['players' => ME])
        ),
      ]),
    ];
  }
}
