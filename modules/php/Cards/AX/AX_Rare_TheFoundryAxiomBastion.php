<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_TheFoundryAxiomBastion extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_29_R1',
      'asset' => 'ALT_CORE_B_AX_29_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Foundry, Axiom Bastion'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'flavorText' => clienttranslate(
        'Embedded in the cliff wall, it overlooks the city and reminds everyone who looks up at it that nothing is unreachable if you put your mind to it.'
      ),
      'artist' => 'Jean-Baptiste Andrier',
      'subtypes' => [LANDMARK],
      'effectDesc' => clienttranslate(
        '#{J} Draw a card.#  {T} : The next Character you play from your hand this turn activates its {r} abilities (as if it had been played from Reserve).'
      ),
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(DRAW, ['players' => ME]),
      'effectTap' => FT::ACTION(SPECIAL_EFFECT, [
        'effect' => 'triggerEffectOfNextCharacter',
        'args' => ['type' => CHARACTER, 'from' => HAND, 'effect' => RESERVE],
      ]),
    ];
  }
}
