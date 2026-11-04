<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_FamedMycologist extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_141_R1',
      'asset' => 'ALT_FUGUE_B_MU_141_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Famed Mycologist'),
      'typeline' => clienttranslate('Character - Druid Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Don\'t truffle with me!'),
      'artist' => 'Jasmin Amaral Fernandez',
      'extension' => 'NEJ',
      'subtypes' => [DRUID, SOLDIER],
      'effectDesc' => clienttranslate('#{J}# You may sacrifice a token #or discard a token# from your Reserve to $<SABOTAGE>.'),
      'forest' => 2,
      'mountain' => 3,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectPlayed' => FT::XOR( 
        FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'onlyToken' => true,
          'targetLocation' => [LANDMARK, STORM_LEFT, STORM_RIGHT],
          'n' => 1,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            FT::ACTION(TARGET, [
              'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
              'targetLocation' => [RESERVE],
              'upTo' => true,
              'effect' => FT::ACTION(DISCARD, []),
            ]),
          ),
        ]),
        FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'onlyToken' => true,
          'targetLocation' => [RESERVE],
          'n' => 1,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(TARGET, [
              'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
              'targetLocation' => [RESERVE],
              'upTo' => true,
              'effect' => FT::ACTION(DISCARD, []),
            ]),
          ),
        ]),
      ),
    ];
  }
}
