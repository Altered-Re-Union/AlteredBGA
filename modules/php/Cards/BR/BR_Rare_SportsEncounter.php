<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_SportsEncounter extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_DUSTER_B_YZ_97_R2',
      'asset'  => 'ALT_DUSTER_B_YZ_97_R',

      'faction'  => FACTION_BR,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Sports Encounter"),
      'typeline' => clienttranslate("Spell - Boon Maneuver"),
      'type'  => SPELL,
      'flavorText'  => clienttranslate('"Your victory is a victory for both our peoples!"'),
      'artist' => "Victor Canton",
      'extension' => 'SDU',
      'subtypes'  => [BOON, MANEUVER],
      'effectDesc' => clienttranslate('#$<COOLDOWN>.#  Distribute 2 boosts among any target Characters in play #or in Reserve.#'),
      'supportDesc' => clienttranslate('{D} : <AFTER_YOU>.'),
      'supportIcon' => 'discard',
      'costHand' => 2,
      'costReserve' => 1,
      'changedStats' => ['costReserve'],
      'cooldown' => true,
      'effectPlayed' => FT::SEQ_DISTRIBUTE_GAINS(
        FT::ACTION(TARGET, ['targetLocation' => [STORM_LEFT, STORM_RIGHT, RESERVE], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
        FT::ACTION(TARGET, ['targetLocation' => [STORM_LEFT, STORM_RIGHT, RESERVE], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
      ),
      'effectSupport' => FT::ACTION(AFTER_YOU, []),
    ];
  }
}
