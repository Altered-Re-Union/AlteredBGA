<?php
namespace ALT\Cards\MU;

class MU_Common_FaithfulDog extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_131_C',
      'asset' => 'ALT_FUGUE_B_MU_131_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Faithful Dog'),
      'typeline' => clienttranslate('Character - Animal, Companion'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('He waited, faithful to his master, hoping to hear a single familiar step.'),
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL, COMPANION],
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 1,
    ];
  }
}
