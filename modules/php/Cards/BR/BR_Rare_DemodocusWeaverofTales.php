<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_DemodocusWeaverofTales extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_136_R2',
      'asset' => 'ALT_FUGUE_B_LY_136_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Demodocus, Weaver of Tales'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'artist' => 'Jamin Amaral Fernandez',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('{H} #Reveal the top card of your deck#. You may target a Character #with Base Cost less than or equal to the revealed card\'s Hand Cost#. It gains $<FLEETING>.'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['ocean'],
      'effectHand' => FT::SEQ(
        FT::ACTION(SPECIAL_EFFECT, ['effect' => 'revealTop']),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'upTo' => true,
          'maxBaseCost' => 'revealedCardHandCost',
          'effect' => FT::GAIN(EFFECT, FLEETING),
        ])
      ),
    ];
  }
}
