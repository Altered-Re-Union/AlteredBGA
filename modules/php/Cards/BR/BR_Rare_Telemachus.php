<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_Telemachus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_134_R1',
      'asset' => 'ALT_FUGUE_B_BR_134_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Telemachus'),
      'typeline' => clienttranslate('Character - Noble, Soldier'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('Daring the adventure is a destination in itself.'),
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE, SOLDIER],
      'effectDesc' => clienttranslate('{R} #Target Character# gains 2 boosts.'),
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 2,
      'changedStats' => ['mountain'],
      'effectReserve' => FT::ACTION(TARGET, [
        'effect' => FT::GAIN(EFFECT, BOOST, 2)
      ]),
    ];
  }
}
