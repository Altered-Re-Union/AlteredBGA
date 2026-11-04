<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_GrumpyImp extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_YZ_111_R1',
      'asset'  => 'ALT_EOLE_B_YZ_111_R',

      'faction'  => FACTION_YZ,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Grumpy Imp"),
      'typeline' => clienttranslate("Character - Spirit"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('He will fight like the devil, despite his fear'),
      'artist' => "Jean-Baptiste Andrier",
      'extension' => 'ROC',
      'subtypes'  => [SPIRIT],
      'effectDesc' => clienttranslate('When you discard a card from your hand — If I have no boosts, I gain 1 boost.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand', 'costReserve', 'forest', 'mountain', 'ocean'],
      'effectPassive' => [
        'Discard' => [
          'conditions' => ['isInStorms', 'hasSameOwner', 'isDiscarded:hand:discard', 'hasNoBoost'],
          'output' => FT::GAIN(ME, BOOST)
        ],
      ],
    ];
  }
}
