<?php
namespace ALT\Cards\AX;

class AX_Common_TrailingSeagull extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_131_C',
      'asset' => 'ALT_FUGUE_B_AX_131_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Trailing Seagull'),
      'typeline' => clienttranslate('Character - Animal'),
      'flavorText'  => clienttranslate('To navigate these uncharted waters, Sierra placed beacons on the back of seagulls to triangulate their position.'),
      'artist' => 'Anh Tung',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [ANIMAL],
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 2,
      'costHand' => 1,
      'costReserve' => 1,
    ];
  }
}
