<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_QorganIntelligencer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_137_C',
      'asset' => 'ALT_FUGUE_B_YZ_137_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Qorgan Intelligencer'),
      'typeline' => clienttranslate('Character - Soldier, Rogue'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Fighting fire with fire has always been part of Yzmir\'s philosophy.'),
      'artist' => 'Fahmi Fauzi',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, ROGUE],
      'effectDesc' => clienttranslate('{H} I gain 1 boost.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'effectHand' => FT::GAIN(ME, BOOST, 1),
    ];
  }
}
