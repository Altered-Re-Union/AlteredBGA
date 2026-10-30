<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_IffyNavigator extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_145_C',
      'asset' => 'ALT_FUGUE_B_LY_145_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Iffy Navigator'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Even seasoned sailors struggle in these treacherous sea.'),
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{J} Roll a die. On a 1-3, I switch Expeditions. (I join your other Expedition.)'),
      'forest' => 1,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '1-3' => FT::ACTION(MOVE_CARD, ['cardId' => ME]),
        ],
      ]),
    ];
  }
}
