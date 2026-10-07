<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_LotusEater extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_138_R2',
      'asset' => 'ALT_FUGUE_B_LY_138_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lotus-Eater'),
      'typeline' => clienttranslate('Character - Citizen, Rogue'),
      'type' => CHARACTER,
      'artist' => 'Nestor Papatriantafyllou',
      'extension' => 'NEJ',
      'subtypes' => [CITIZEN, ROGUE],
      'effectDesc' => clienttranslate('#{R}# Roll a die, then $<SABOTAGE> a card with Reserve Cost less than or equal to the die\'s result.'),
      'forest' => 3,
      'mountain' => 0,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 2,
      'effectReserve' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '1+' => FT::ACTION(TARGET, [
            'targetType' => [SPELL, CHARACTER, TOKEN, PERMANENT],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'maxReserveCost' => 'die',
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        ],
      ]),
    ];
  }
}
