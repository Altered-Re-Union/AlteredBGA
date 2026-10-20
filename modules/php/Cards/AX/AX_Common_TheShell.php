<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Common_TheShell extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_DUSTER_B_AX_96_C',
      'asset'  => 'ALT_DUSTER_B_AX_96_C',

      'faction'  => FACTION_AX,
      'rarity'  => RARITY_COMMON,
      'name'  => clienttranslate("The Shell"),
      'typeline' => clienttranslate("Character - Robot"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('"Does this physical shell define me?"'),
      'artist' => "Taras Susak",
      'extension' => 'SDU',
      'subtypes'  => [ROBOT],
      'effectDesc' => clienttranslate('{R} You may put a card from your hand in Reserve to draw a card.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 3,
      'effectReserve' => FT::ACTION(
        TARGET,
        [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'effect' => FT::SEQ(FT::DISCARD_TO_RESERVE(), FT::ACTION(DRAW, ['players' => ME])),
        ],
        ['optional' => true]
      ),
    ];
  }
}
