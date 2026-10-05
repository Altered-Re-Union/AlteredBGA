<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_IffyNavigator extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_145_R2',
      'asset' => 'ALT_FUGUE_B_LY_145_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Iffy Navigator'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Even seasoned sailors struggle in these treacherous sea.'),
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{J} Roll a die. On a #1-2#, I switch Expeditions.'),
      'forest' => 1,
      'mountain' => 4,
      'ocean' => 4,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain', 'ocean'],
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '1-2' => FT::ACTION(MOVE_CARD, ['cardId' => ME]),
        ],
      ]),
    ];
  }
}
