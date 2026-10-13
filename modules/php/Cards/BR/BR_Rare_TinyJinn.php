<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_TinyJinn extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_06_R1',
      'asset' => 'ALT_CORE_B_BR_06_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Tiny Jinn'),
      'typeline' => clienttranslate('Character - Elemental'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('It may be a fire today — tomorrow it will be ashes.'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [ELEMENTAL],
      'effectDesc' => clienttranslate(
        'If I would leave the Expedition zone while I\'m <BOOSTED>, put me in my owner\'s Mana zone instead (as an exhausted Mana Orb).  {R} I gain 1 boost.'
      ),
      'forest' => 0,
      'mountain' => 2,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 2,
      'changedStats' => ['mountain', 'costHand', 'costReserve'],
      'effectReserve' => FT::GAIN(ME, BOOST),
      // 'leaveExpeditionBoostedToMana' => true,
      'effectPassive' => [
        'LeaveExpedition' => [
          'condition' => 'hasBoost',
          'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => MANA, 'tapped' => true]),
        ],
      ],
    ];
  }
}
