<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_Hector extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_147_C',
      'asset' => 'ALT_FUGUE_B_OR_147_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Hector'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Such a peaceful soul still had to take up arms to lead Troy\'s defense.'),
      'artist' => 'Damian Audino',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{H} Target up to two other Soldiers, they each gain 1 boost.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, TOKEN],
        'subType' => SOLDIER,
        'upTo' => true,
        'n' => 2,
        'excludeSelf' => true,
        'targetLocation' => [STORMS],
        'effect' => FT::GAIN(TARGET, BOOST, 1),
      ]),
    ];
  }
}
