<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_Eurylochus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_136_C',
      'asset' => 'ALT_FUGUE_B_BR_136_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Eurylochus'),
      'typeline' => clienttranslate('Character - Soldier, Rogue'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('"I though the point of having Odysseus as guide was to avoid all these perils..."'),
      'artist' => 'Alexandre Bonvalot',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, ROGUE],
      'effectDesc' => clienttranslate('{R} $<SABOTAGE>.'),
      'forest' => 0,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 3,
      'effectReserve' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
    ];
  }
}
