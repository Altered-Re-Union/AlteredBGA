<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Exalted_TreacherousLoop extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_147_E',
      'asset' => 'ALT_FUGUE_B_YZ_147_E',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Treacherous Loop'),
      'typeline' => clienttranslate('Spell - Maneuver'),
      'type' => SPELL,
      'artist' => 'Andy Joffrit',
      'extension' => 'NEJ',
      'subtypes' => [MANEUVER],
      'effectDesc' => clienttranslate('$<FLEETING>.  Ready up to eight Mana Orbs.'),
      'costHand' => 7,
      'costReserve' => 7,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [MANA],
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'isTapped' => true,
          'upTo' => true,
          'n' => 8,
          'allIds' => true,
          'effect' => FT::ACTION(READY, ['cardId' => EFFECT]),
        ]),
      ),
    ];
  }
}
