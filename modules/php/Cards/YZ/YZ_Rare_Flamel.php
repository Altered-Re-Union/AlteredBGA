<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_Flamel extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_14_R1',
      'asset' => 'ALT_CORE_B_YZ_14_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Flamel'),
      'typeline' => clienttranslate('Character - Scholar'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'Rubedo is the final stage of alchemy, where one\'s substance is reborn like a phoenix before returning to the world.'
      ),
      'artist' => 'Nestor Papatriantafyllou',
      'subtypes' => [SCHOLAR],
      'effectDesc' => clienttranslate('{H} #$<RESUPPLY>.# Then, you may return a Spell from your Reserve to your hand.'),
      'supportDesc' => clienttranslate(
        '{D} : The next Spell you play this turn costs {1} less. (Discard me from Reserve to do this.)'
      ),
      'forest' => 4,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 3,
      'effectHand' => FT::SEQ(
        FT::ACTION(RESUPPLY, []),
        FT::ACTION(
          TARGET,
          [
            'targetLocation' => [RESERVE],
            'targetPlayer' => ME,
            'targetType' => [SPELL],
            'effect' => FT::RETURN_TO_HAND(),
          ],
          ['optional' => true]
        )
      ),
      'supportIcon' => 'discard',
      'effectSupport' => [
        'action' => SPECIAL_EFFECT,
        'args' => ['effect' => 'costReduction', 'args' => ['type' => SPELL, 'reduction' => 1]],
      ],
    ];
  }
}
