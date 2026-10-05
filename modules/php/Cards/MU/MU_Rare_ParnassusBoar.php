<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_ParnassusBoar extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_136_R1',
      'asset' => 'ALT_FUGUE_B_MU_136_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Parnassus Boar'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'artist' => 'Benoit Barraqué-Curie',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL],
      'effectDesc' => clienttranslate('{H} You may put #target Permanent# in its owner\'s Mana zone (as an exhausted Mana Orb).'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['ocean'],
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [PERMANENT],
        'targetLocation' => [LANDMARK, STORM_LEFT, STORM_RIGHT],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, [
          'cardId' => 'target', 
          'destination' => MANA, 
          'tapped' => true,
        ]),
      ]),
    ];
  }
}
