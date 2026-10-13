<?php

namespace ALT\Cards\MU;

class MU_Rare_Cernunnos extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_MU_14_R1',
      'asset' => 'ALT_CORE_B_MU_14_R',

      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Cernunnos'),
      'typeline' => clienttranslate('Character - Deity Druid'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('You can feel it in the trees, deep beneath their roots – the very heartbeat of nature.'),
      'artist' => 'Ba Vo',
      'subtypes' => [DEITY, DRUID],
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 4,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['costHand'],
    ];
  }
}
