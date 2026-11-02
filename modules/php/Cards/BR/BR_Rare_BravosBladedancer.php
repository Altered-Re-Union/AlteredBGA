<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_BravosBladedancer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_BR_16_R1',
      'asset' => 'ALT_CORE_B_BR_16_R',

      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Bravos Bladedancer'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"It seems Thoth has never seen me duel."'),
      'artist' => 'Taras Susak',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate(
        '$<SEASONED_ME_FS>.  {J} I gain 1 boost.  #{R} If I have 4 boosts or less, I lose <FLEETING_CHAR>.#'
      ),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 3,
      'seasoned' => true,
      'effectPlayed' => FT::GAIN(ME, BOOST),
      'effectReserve' => FT::ACTION(CHECK_CONDITION, ['condition' => 'hasBoost:4:LTE', 'effect' => FT::LOOSE($this, FLEETING)]),
      'blockAutomaticAction' => [GAIN => [BOOST => 1], CHECK_CONDITION => ['hasBoost:4:LTE']],
    ];
  }
}
