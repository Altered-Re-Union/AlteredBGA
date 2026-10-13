<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_TheKraken extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_17_R1',
      'asset' => 'ALT_CORE_B_YZ_17_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Kraken'),
      'typeline' => clienttranslate('Character - Leviathan'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'The Kraken tears open the surface, sending plumes of water crashing down on the troops in an apocalyptic deluge.'
      ),
      'artist' => 'Fahmi Fauzi',
      'subtypes' => [LEVIATHAN],
      'effectDesc' => clienttranslate(
        '$<GIGANTIC>.  {J} Sacrifice #one Character#.  All regions are {E} and lose their other types.'
      ),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 8,
      'costHand' => 7,
      'costReserve' => 7,
      'gigantic' => true,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER, TOKEN],
        'n' => 1,
        'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
      ]),
      'updateExpeditions' => ['type' => 'all', 'regionsRemove' => [MOUNTAIN, FOREST], 'regionsAdd' => [OCEAN]],
    ];
  }
}
