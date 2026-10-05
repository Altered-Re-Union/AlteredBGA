<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_ReefDolphin extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_132_R2',
      'asset' => 'ALT_FUGUE_B_LY_132_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Reef Dolphin'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL],
      'effectDesc' => clienttranslate('{R} You may discard a card from your Reserve to draw a card.'),
      'forest' => 0,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain'],
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
