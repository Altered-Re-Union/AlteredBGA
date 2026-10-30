<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_MenelausBringerofWar extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_137_C',
      'asset' => 'ALT_FUGUE_B_OR_137_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Menelaus, Bringer of War'),
      'typeline' => clienttranslate('Character - Noble, Soldier'),
      'flavorText' => clienttranslate('He calls it justice. Most call it war.'),
      'artist' => 'Ahn Tung',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [NOBLE, SOLDIER],
      'effectDesc' => clienttranslate('{H} You may discard a Character from your Reserve to draw a card.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 4,
      'costHand' => 4,
      'costReserve' => 3,
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, TOKEN],
        'targetPlayer' => ME,
        'targetLocation' => [RESERVE],
        'effect' => FT::SEQ(
          FT::ACTION(DISCARD, []),
          FT::ACTION(DRAW, ['players' => ME]),
        ),
      ], ['optional' => true]),
    ];
  }
}
