<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_Ino extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_133_R2',
      'asset' => 'ALT_FUGUE_B_MU_133_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ino'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('When all hope is lost, there is often a helping hand to lift us back up.'),
      'artist' => 'Eilene Cherie',
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('#{H} Discard a card from your hand.#'),
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand'],
      'effectHand' => FT::ACTION(DISCARD, ['source' => HAND]),
    ];
  }
}
