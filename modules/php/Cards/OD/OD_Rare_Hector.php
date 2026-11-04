<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_Hector extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_147_R1',
      'asset' => 'ALT_FUGUE_B_OR_147_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Hector'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Such a peaceful soul still had to take up arms to lead Troy\'s defense.'),
      'artist' => 'Damian Audino',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{H} #Distribute 3 boosts# among any target Soldiers in play or #in Reserve#.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 3,
      'costReserve' => 1,
      'changedStats' => ['forest', 'mountain', 'ocean', 'costReserve'],
      'effectHand' => FT::SEQ(
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'subType' => SOLDIER,
          'targetLocation' => [...STORMS, RESERVE],
          'effect' => FT::GAIN(TARGET, BOOST, 1),
        ]),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'subType' => SOLDIER,
          'targetLocation' => [...STORMS, RESERVE],
          'effect' => FT::GAIN(TARGET, BOOST, 1),
        ]),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'subType' => SOLDIER,
          'targetLocation' => [...STORMS, RESERVE],
          'effect' => FT::GAIN(TARGET, BOOST, 1),
        ]),
      ),
    ];
  }
}
