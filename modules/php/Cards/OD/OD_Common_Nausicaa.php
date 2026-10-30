<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_Nausicaa extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_132_C',
      'asset' => 'ALT_FUGUE_B_OR_132_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Nausicaa'),
      'typeline' => clienttranslate('Character - Noble Soldier'),
      'type' => CHARACTER,
      'artist' => 'Andy Jauffrit',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE, SOLDIER],
      'effectDesc' => clienttranslate('{H} If there\'s a Soldier in your Reserve, Resupply.'),
      'forest' => 1,
      'mountain' => 0,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'effectHand' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasReserve:soldier',
        'effect' => FT::ACTION(RESUPPLY, []),
      ]),
    ];
  }
}
