<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_FamedMycologist extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_141_C',
      'asset' => 'ALT_FUGUE_B_MU_141_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Famed Mycologist'),
      'typeline' => clienttranslate('Character - Druid Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Don\'t truffle with me!'),
      'artist' => 'Jasmin Amaral Fernandez',
      'extension' => 'NEJ',
      'subtypes' => [DRUID, SOLDIER],
      'effectDesc' => clienttranslate('{H} You may sacrifice a token to $<SABOTAGE>.'),
      'forest' => 2,
      'mountain' => 3,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER, TOKEN, PERMANENT],
        'onlyToken' => true,
        'upTo' => true,
        'effect' => FT::SEQ(
          FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
          FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        ),
      ])
    ];
  }
}
