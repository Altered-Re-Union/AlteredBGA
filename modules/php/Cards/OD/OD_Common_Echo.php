<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_Echo extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_148_C',
      'asset' => 'ALT_FUGUE_B_OR_148_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Echo'),
      'typeline' => clienttranslate('Character - Soldier, Companion'),
      'type' => CHARACTER,
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, COMPANION],
      'effectDesc' => clienttranslate('{R} If there are two or more other Soldiers in your Expeditions, I gain 1 boost. (I\'m created in Reserve. You can play me in an Expedition. Remove me from the game if I would go anywhere else.)'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costReserve' => 1,
      'token' => true,
      'effectReserve' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasControl:soldier:1:true:all:LTE',
        'effect' => null,
        'oppositeEffect' => FT::GAIN(ME, BOOST, 1),
      ]),
    ];
  }
}
