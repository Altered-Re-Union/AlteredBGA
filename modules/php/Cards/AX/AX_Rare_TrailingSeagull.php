<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_TrailingSeagull extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_131_R1',
      'asset' => 'ALT_FUGUE_B_AX_131_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Trailing Seagull'),
      'typeline' => clienttranslate('Character - Animal'),
      'flavorText'  => clienttranslate('To navigate these uncharted waters, Sierra placed beacons on the back of seagulls to triangulate their position.'),
      'artist' => 'Anh Tung',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [ANIMAL],
      'effectDesc' => clienttranslate('#{R} I gain 1 boost.#'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 2,
      'costHand' => 1,
      'costReserve' => 1,
      'effectReserve' => FT::GAIN(ME, BOOST),
    ];
  }
}
