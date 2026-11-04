<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Common_SportsEncounter extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_DUSTER_B_YZ_97_C',
      'asset'  => 'ALT_DUSTER_B_YZ_97_C',

      'faction'  => FACTION_YZ,
      'rarity'  => RARITY_COMMON,
      'name'  => clienttranslate("Sports Encounter"),
      'typeline' => clienttranslate("Spell - Boon Maneuver"),
      'type'  => SPELL,
      'flavorText'  => clienttranslate('"Your victory is a victory for both our peoples!"'),
      'artist' => "Victor Canton",
      'extension' => 'SDU',
      'subtypes'  => [BOON, MANEUVER],
      'effectDesc' => clienttranslate('Distribute 2 boosts among any target Characters in play.'),
      'supportDesc' => clienttranslate('{D} : <AFTER_YOU>. (Discard me from Reserve to end your turn as if you had played a card.)'),
      'supportIcon' => 'discard',
      'costHand' => 2,
      'costReserve' => 4,
      'effectPlayed' => FT::SEQ_DISTRIBUTE_GAINS(
        FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
        FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
      ),
      'effectSupport' => FT::ACTION(AFTER_YOU, []),
    ];
  }
}
