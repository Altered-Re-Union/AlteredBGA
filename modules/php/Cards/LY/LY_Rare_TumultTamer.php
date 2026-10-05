<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_TumultTamer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_137_R1',
      'asset' => 'ALT_FUGUE_B_LY_137_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Tumult Tamer'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('#{J}# Roll a die. On a 4+, draw a card.'),
      'forest' => 3,
      'mountain' => 4,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '4+' => FT::ACTION(DRAW, ['players' => ME]),
        ],
      ]),
    ];
  }
}
