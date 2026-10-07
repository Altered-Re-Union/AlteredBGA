<?php
namespace ALT\Cards\MU;

class MU_Common_Ino extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_133_C',
      'asset' => 'ALT_FUGUE_B_MU_133_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Ino'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('When all hope is lost, there is often a helping hand to lift us back up.'),
      'artist' => 'Eilene Cherie',
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'forest' => 1,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 1,
    ];
  }
}
