<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_Toot extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_148_C',
      'asset' => 'ALT_FUGUE_B_MU_148_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Toot'),
      'typeline' => clienttranslate('Character - Plant, Companion'),
      'type' => CHARACTER,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [PLANT, COMPANION],
      'effectDesc' => clienttranslate('{R} I gain $<ANCHORED>. (I\'m created in Reserve. You can play me in an Expedition. Remove me from the game if I would go anywhere else.)'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costReserve' => 1,
      'token' => true,
      'effectReserve' => FT::GAIN(ME, ANCHORED),
    ];
  }
}
