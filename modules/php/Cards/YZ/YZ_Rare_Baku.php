<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_Baku extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_10_R1',
      'asset' => 'ALT_CORE_B_YZ_10_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Baku'),
      'typeline' => clienttranslate('Character - Spirit'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        '"I’ve seen it myself! \'Baku! Baku!\', it says, eating up ghosts as though they were candies."'
      ),
      'artist' => 'Zero Wen',
      'subtypes' => [SPIRIT],
      'effectDesc' => clienttranslate('#{J}# Target opponent discards a card from their hand.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['costReserve'],
      'effectPlayed' => FT::ACTION(TARGET_PLAYER, [
        'effect' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ]),
    ];
  }
}
