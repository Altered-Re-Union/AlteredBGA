<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_AeolusWinds extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_142_C',
      'asset' => 'ALT_FUGUE_B_AX_142_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Aeolus\' Winds'),
      'typeline' => clienttranslate('Spell'),
      'type' => SPELL,
      'artist' => 'Gamon Studio',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('$<FLEETING>.  Send to Reserve target Character with Base Cost less than or equal to the number of cards in your Landmarks.'),
      'costHand' => 2,
      'costReserve' => 2,
      'fleeting' => true,
      'effectPlayed' => FT::SEQ(FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 'landmarks',
          'effect' => FT::DISCARD_TO_RESERVE(),
        ])),
    ];
  }
}
