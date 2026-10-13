<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_ManaChanneling extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_27_R1',
      'asset' => 'ALT_CORE_B_BR_27_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Mana Channeling'),
      'typeline' => clienttranslate('Spell - Conjuration'),
      'type' => SPELL,
      'flavorText' => clienttranslate('There\'s only one way to turn the tide: by becoming stronger.'),
      'artist' => 'Zero Wen',
      'subtypes' => [CONJURATION],
      'effectDesc' => clienttranslate('Put the top #two cards# of your deck in your Mana zone (as exhausted Mana Orbs).'),
      'costHand' => 5,
      'costReserve' => 5,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(DRAW_MANA, ['n' => 2]),
    ];
  }
}
