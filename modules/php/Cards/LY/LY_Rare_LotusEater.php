<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_LotusEater extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_138_R1',
      'asset' => 'ALT_FUGUE_B_LY_138_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lotus-Eater'),
      'typeline' => clienttranslate('Character - Citizen, Rogue'),
      'type' => CHARACTER,
      'artist' => 'Nestor Papatriantafyllou',
      'extension' => 'NEJ',
      'subtypes' => [CITIZEN, ROGUE],
      'effectDesc' => clienttranslate('{H} Roll a die, then $<SABOTAGE> a card with Reserve Cost less than or equal to the die\'s result. #On a 6+, you may also Sabotage another card.#'),
      'forest' => 3,
      'mountain' => 0,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '1-5' => FT::ACTION(TARGET, [
            'targetType' => [SPELL, CHARACTER, TOKEN, PERMANENT],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'maxReserveCost' => 'die',
            'effect' => FT::ACTION(DISCARD, []),
          ]),
          '6+' => FT::SEQ(
            FT::ACTION(TARGET, [
              'targetType' => [SPELL, CHARACTER, TOKEN, PERMANENT],
              'targetLocation' => [RESERVE],
              'upTo' => true,
              'maxReserveCost' => 'die',
              'effect' => FT::ACTION(DISCARD, []),
            ]),
            FT::ACTION(TARGET, [
              'targetType' => [SPELL, CHARACTER, TOKEN, PERMANENT],
              'targetLocation' => [RESERVE],
              'upTo' => true,
              'effect' => FT::ACTION(DISCARD, []),
            ]),
          ),
        ],
      ]),
    ];
  }
}
