<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_KelonicGenerator extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_27_R1',
      'asset' => 'ALT_CORE_B_AX_27_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Kelonic Generator'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate('#{1}#, {T} : Draw a card.'),
      'flavorText' => clienttranslate('"Suspira\'s mines will soon run dry. Your precious Kelon will run dry."'),
      'artist' => 'Jean-Baptiste Andrier',

      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costHand', 'costReserve'],

      'effectTap' => FT::SEQ(FT::ACTION(PAY, ['pay' => 1]), FT::ACTION(DRAW, ['players' => ME])),
    ];
  }
}
