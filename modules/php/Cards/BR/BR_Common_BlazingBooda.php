<?php
namespace ALT\Cards\BR;

class BR_Common_BlazingBooda extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_148_C',
      'asset' => 'ALT_FUGUE_B_BR_148_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Blazing Booda'),
      'typeline' => clienttranslate('Token Character - Companion'),
      'type' => CHARACTER,
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'subtypes' => [COMPANION],
      'effectDesc' => clienttranslate('(I\'m created in Reserve. You can play me in an Expedition. Remove me from the game if I would go anywhere else.)'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costReserve' => 2,
      'token' => true,
    ];
  }
}
