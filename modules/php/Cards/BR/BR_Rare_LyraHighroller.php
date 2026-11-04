<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_LyraHighroller extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_140_R2',
      'asset' => 'ALT_FUGUE_B_LY_140_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lyra Highroller'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Sometimes, you have to play big.'),
      'artist' => 'Benoit Barraqué-Currie',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('{H} #Reveal the top card of your deck. If it\'s Hand Cost is:#  #• {4} or more, I gain Anchored.#  • #{1} or less#, I gain Fleeting.'),
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'effectHand' => FT::SEQ(
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'revealTop']),
        FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'gainOnRevealedHandCost',
          'args' => [
            '4+' => ANCHORED,
            '0-1' => FLEETING,
          ],
        ])
      ),
    ];
  }
}
