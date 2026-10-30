<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_BetweenaRockandaHardPlace extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_145_C',
      'asset' => 'ALT_FUGUE_B_YZ_145_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Between a Rock and a Hard Place'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'artist' => 'Gamon Studio',
      'extension' => 'NEJ',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  Target player chooses their Hero or Companion Expedition, then sacrifices all Characters in it.'),
      'costHand' => 4,
      'costReserve' => 4,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(TARGET_EXPEDITION, [
            'players' => ME,
            'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'sacrificeAllCharacters']),
          ]),
        ]),
      ),
    ];
  }
}
