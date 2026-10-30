<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_BlindingBlow extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_142_C',
      'asset' => 'ALT_FUGUE_B_OR_142_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Blinding Blow'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  You may play me for {1} less. If you do, I can only target a Gigantic Character.  Send target Character with minimum Base Cost {3} to Reserve.'),
      'costHand' => 3,
      'costReserve' => 3,
      'costReductionLimitation' => 1,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'effect' => FT::DISCARD_TO_RESERVE()
        ])
      ),
      'effectPlayedLimited' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'giganticOnly' => true,
          'effect' => FT::DISCARD_TO_RESERVE()
        ])
      ),
    ];
  }
}
