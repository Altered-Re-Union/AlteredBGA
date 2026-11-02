<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_ScheriasRoyalty extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_133_R1',
      'asset' => 'ALT_FUGUE_B_LY_133_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Scheria\'s Royalty'),
      'typeline' => clienttranslate('Character - Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Your journey will be arduous. Take this respite, and enjoy our hospitality."'),
      'artist' => 'Zero Wen',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE],
      'effectDesc' => clienttranslate('#{H} Roll a die. On a 4+, I gain 1 boost.#'),
      'forest' => 3,
      'mountain' => 0,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(ROLL_DIE, [
        'effect' => [
          '4+' => FT::GAIN(ME, BOOST),
        ],
      ]),
    ];
  }
}
