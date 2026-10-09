<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_Ino extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_133_R1',
      'asset' => 'ALT_FUGUE_B_MU_133_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ino'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('When all hope is lost, there is often a helping hand to lift us back up.'),
      'artist' => 'Eilene Cherie',
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('#{H} Target opponent draws a card.#'),
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand'],
      'effectHand' => FT::ACTION(DRAW, ['players' => OPPONENT]),
    ];
  }
}
