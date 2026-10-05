<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_ReefDolphin extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_132_C',
      'asset' => 'ALT_FUGUE_B_LY_132_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Reef Dolphin'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL],
      'effectDesc' => clienttranslate('{R} You may discard a card from your Reserve to draw a card.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'effectReserve' => FT::ACTION(
        TARGET,
        [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetPlayer' => ME,
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::SEQ(FT::ACTION(DISCARD, []), FT::ACTION(DRAW, ['players' => ME])),
        ],
        ['optional' => true]
      ),
    ];
  }
}
