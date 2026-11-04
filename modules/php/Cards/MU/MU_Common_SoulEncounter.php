<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Common_SoulEncounter extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_DUSTER_B_MU_98_C',
      'asset'  => 'ALT_DUSTER_B_MU_98_C',

      'faction'  => FACTION_MU,
      'rarity'  => RARITY_COMMON,
      'name'  => clienttranslate("Soul Encounter"),
      'typeline' => clienttranslate("Spell - Boon Maneuver"),
      'type'  => SPELL,
      'flavorText'  => clienttranslate('"Let us offer the Musubi to the Reka, so that new Exalts might be born!"'),
      'artist' => "Khoa Viet",
      'extension' => 'SDU',
      'subtypes'  => [BOON, MANEUVER],
      'effectDesc' => clienttranslate('Distribute 4 boosts among any target Characters in play or in Reserve.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::SEQ_DISTRIBUTE_GAINS(
        FT::ACTION(TARGET, ['targetLocation' => [RESERVE, STORM_LEFT, STORM_RIGHT], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
        FT::ACTION(TARGET, ['targetLocation' => [RESERVE, STORM_LEFT, STORM_RIGHT], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
        FT::ACTION(TARGET, ['targetLocation' => [RESERVE, STORM_LEFT, STORM_RIGHT], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
        FT::ACTION(TARGET, ['targetLocation' => [RESERVE, STORM_LEFT, STORM_RIGHT], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
      )
    ];
  }
}
