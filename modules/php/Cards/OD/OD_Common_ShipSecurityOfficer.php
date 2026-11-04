<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_ShipSecurityOfficer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_138_C',
      'asset' => 'ALT_FUGUE_B_OR_138_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Ship Security Officer'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{H} You may return target card in another player\'s Reserve to its owner\'s hand.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(TARGET,
        [
          'targetLocation' => [RESERVE],
          'targetPlayer' => OPPONENT,
          'targetType' => [CHARACTER, TOKEN, PERMANENT, SPELL],
          'excludeSelf' => true,
          'effect' => FT::RETURN_TO_HAND(),
        ],
        ['optional' => true]
      ),
    ];
  }
}
