<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_FaithfulDog extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_131_R1',
      'asset' => 'ALT_FUGUE_B_MU_131_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Faithful Dog'),
      'typeline' => clienttranslate('Character - Animal, Companion'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('He waited, faithful to his master, hoping to hear a single familiar step.'),
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL, COMPANION],
      'effectDesc' => clienttranslate('#{R} I gain 1 boost and $<ANCHORED>.#'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 2,
      'changedStats' => ['ocean', 'costReserve'],
      'effectReserve' => FT::SEQ(
        FT::GAIN(ME, BOOST), 
        FT::GAIN(ME, ANCHORED)
      ),
    ];
  }
}
