<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_ScheriasRoyalty extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_133_R2',
      'asset' => 'ALT_FUGUE_B_LY_133_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Scheria\'s Royalty'),
      'typeline' => clienttranslate('Character - Noble'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"Your journey will be arduous. Take this respite, and enjoy our hospitality."'),
      'artist' => 'Zero Wen',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE],
      'effectDesc' => clienttranslate('#{H} Reveal the top card of your deck. If it\'s a Spell, I gain 1 boost.#'),
      'forest' => 3,
      'mountain' => 0,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'effectHand' => FT::SEQ(
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'revealTop']),
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'gainOnRevealedType', 'args' => ['type' => SPELL]])
      )
    ];
  }
}
