<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_Aja extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_17_R1',
      'asset' => 'ALT_CORE_B_MU_17_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Aja'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'She is the soul of the forest, the patron of herbal medicine. From herbs and roots, she mixes potent potions.'
      ),
      'artist' => 'Rémi Jacquot',
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate(
        '#{J}# Each player puts the top card of their deck in their Mana zone (as an exhausted Mana Orb).'
      ),
      'supportDesc' => clienttranslate(
        '#{D} : Target Character with Hand Cost {3} or less gains <ANCHORED>.# (Discard me from Reserve to do this.)'
      ),
      'forest' => 4,
      'mountain' => 5,
      'ocean' => 4,
      'costHand' => 4,
      'costReserve' => 4,
      'effectPlayed' => FT::ACTION(DRAW, ['location' => MANA, 'tapped' => true]),
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'effect' => FT::GAIN(EFFECT, ANCHORED)]),
    ];
  }
}
