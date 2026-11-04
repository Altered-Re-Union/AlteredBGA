<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_ShipSecurityOfficer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_138_R2',
      'asset' => 'ALT_FUGUE_B_OR_138_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ship Security Officer'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('#{J}# You may return target card in another player\'s Reserve to its owner\'s hand. Your opponents can\'t play cards with that name this Day.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(TARGET,
        [
          'targetLocation' => [RESERVE],
          'targetPlayer' => OPPONENT,
          'targetType' => [CHARACTER, TOKEN, PERMANENT, SPELL],
          'excludeSelf' => true,
          'effect' => FT::SEQ(
            FT::RETURN_TO_HAND(),
            FT::ACTION(SPECIAL_EFFECT, ['effect' => 'blockOpponentsCardNameThisDay']),
          ),
        ],
        ['optional' => true]
      ),
    ];
  }
}
