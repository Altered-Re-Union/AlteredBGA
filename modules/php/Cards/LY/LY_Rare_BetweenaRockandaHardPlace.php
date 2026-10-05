<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_BetweenaRockandaHardPlace extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_145_R2',
      'asset' => 'ALT_FUGUE_B_YZ_145_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Between a Rock and a Hard Place'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  Target player chooses their Hero or Companion Expedition, then sacrifices all Characters in it.'),
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['costHand', 'costReserve'],
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
