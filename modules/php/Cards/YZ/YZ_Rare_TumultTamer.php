<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_TumultTamer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_137_R2',
      'asset' => 'ALT_FUGUE_B_LY_137_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Tumult Tamer'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('#{J} Reveal the top card of your deck. If it\'s a Spell#, draw a card.'),
      'forest' => 3,
      'mountain' => 4,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'revealTop']),
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'drawRevealed', 'args' => ['type' => SPELL]])
      ),
    ];
  }
}
