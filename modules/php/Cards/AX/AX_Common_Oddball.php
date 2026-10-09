<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_Oddball extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_148_C',
      'asset' => 'ALT_FUGUE_B_AX_148_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Oddball'),
      'typeline' => clienttranslate('Token Character - Robot Companion'),
      'type' => CHARACTER,
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'subtypes' => [ROBOT, COMPANION],
      'effectDesc' => clienttranslate('I cost {2} less if you played a <Construction> this Day. (I\'m created in Reserve. You can play me in an Expedition. Remove me from the game if I would go anywhere else.)'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costReserve' => 2,
      'costReductionIfConstructionPlayed' => 2,
      'token' => true,
    ];
  }
}