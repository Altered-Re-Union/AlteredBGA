<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_Sakarabru extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_18_R1',
      'asset' => 'ALT_CORE_B_YZ_18_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Sakarabru'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('{H} Your opponent\'s Expedition facing me moves backwards one region.'),
      'supportDesc' => clienttranslate('#{D} : Draw a card.# (Discard me from Reserve to do this.)'),
      'supportIcon' => 'discard',
      'flavorText' => clienttranslate('When such a terrifying being appears in your path, taking a step back is only natural.'),
      'artist' => 'Gael Giudicelli',

      'forest' => 5,
      'mountain' => 5,
      'ocean' => 5,
      'costHand' => 7,
      'costReserve' => 4,
      'changedStats' => ['forest', 'mountain', 'ocean'],
      'effectHand' => FT::ACTION(MOVE_EXPEDITION, ['n' => -1, 'expedition' => [EFFECT], 'pId' => OPPONENT]),
      'effectSupport' => FT::ACTION(DRAW, ['players' => ME]),
    ];
  }
}
