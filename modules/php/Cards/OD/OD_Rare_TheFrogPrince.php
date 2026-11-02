<?php

namespace ALT\Cards\OD;

class OD_Rare_TheFrogPrince extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_09_R1',
      'asset' => 'ALT_CORE_B_OR_09_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Frog Prince'),
      'typeline' => clienttranslate('Character - Bureaucrat Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Thankfully, he doesn\'t seal his contracts with a kiss.'),
      'artist' => 'Gaga Zhou',
      'subtypes' => [BUREAUCRAT, NOBLE],
      'forest' => 2,
      'mountain' => 0,
      'ocean' => 2,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['forest', 'ocean', 'costHand', 'costReserve'],
    ];
  }
}
