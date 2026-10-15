<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_TheHunger extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_BISE_B_YZ_56_R1',
      'asset'  => 'ALT_BISE_B_YZ_56_R',

      'faction'  => FACTION_YZ,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("The Hunger"),
      'typeline' => clienttranslate("Character - Spirit"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('The City has a soul, and it is famished.'),
      'artist' => "Taras Susak",
      'extension' => 'WFTM',
      'subtypes'  => [SPIRIT],
      'effectDesc' => clienttranslate('{H} Discard all other cards in play or in Reserve.  When a card goes to the discard pile — I gain 1 boost.  #If I\'m <FLEETING>, I am <GIGANTIC>.#'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 8,
      'costReserve' => 6,
      'changedStats' => ['costHand', 'costReserve'],
      // 'effectHand' => FT::ACTION(TARGET, [
      //   'n' => INFTY,
      //   'targetLocation' => [STORM_RIGHT, STORM_LEFT, LANDMARK, RESERVE],
      //   'targetType' => [TOKEN, CHARACTER, SPELL, PERMANENT],
      //   'excludeSelf' => true,
      //   'ignoreTough' => true,
      //   'effect' => FT::ACTION(DISCARD, [])
      // ]),
      'effectHand' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'hunger']),
      'effectPassive' => [
        'Discard' => [
          'conditions' => ['isInStorms','isDiscarded::discard', 'isNotToken'],
          'output' => FT::GAIN(ME, BOOST)
        ],
        'ChooseAssignment' => [
          'conditions' => ['isInStorms', 'isAfternoon', 'isFromReserve', 'isSupportEffect'],
          'output' => FT::GAIN(ME, BOOST)
        ],
      ],
      'dynamicGigantic' => '1:hasFleeting',
    ];
  }
}
