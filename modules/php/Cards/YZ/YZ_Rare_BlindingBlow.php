<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_BlindingBlow extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_142_R2',
      'asset' => 'ALT_FUGUE_B_OR_142_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Blinding Blow'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  You may play me for {1} less. If you do, I can only target a Gigantic Character.  #Choose one:#  • Send to Reserve #target Character.#  • #Discard target Permanent.#'),
      'costHand' => 3,
      'costReserve' => 3,
      'costReductionLimitation' => 1,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, TOKEN, PERMANENT],
            'effect' => FT::DISCARD_TO_RESERVE()
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'effect' => FT::ACTION(DISCARD, []),
          ])
        ),
      ),
      'effectPlayedLimited' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'giganticOnly' => true,
          'effect' => FT::DISCARD_TO_RESERVE()
        ]),
      ),
    ];
  }
}
