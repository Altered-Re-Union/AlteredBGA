<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_AeolusWinds extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_142_R2',
      'asset' => 'ALT_FUGUE_B_AX_142_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Aeolus\' Winds'),
      'typeline' => clienttranslate('Spell'),
      'type' => SPELL,
      'artist' => 'Gamon Studio',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('$<FLEETING>.  Send to Reserve target Character with Base Cost less than or equal to the number of cards in your Landmarks.#  If there are three or more cards in your Landmarks, discard it instead.#'),
      'costHand' => 2,
      'costReserve' => 2,
      'fleeting' => true,
      'effectPlayed' => FT::SEQ(FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 'landmarks',
          'effect' => FT::ACTION(CHECK_CONDITION, [
            'condition' => 'hasControl:landmark:3',
            'effect' => FT::ACTION(DISCARD, []),
            'oppositeEffect' => FT::DISCARD_TO_RESERVE(),
          ]),
        ])),
    ];
  }
}


