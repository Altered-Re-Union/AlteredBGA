<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_Agamemnon extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_139_C',
      'asset' => 'ALT_FUGUE_B_YZ_139_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Agamemnon'),
      'typeline' => clienttranslate('Character - Noble, Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('If his hand does not tremble, Iphigenia\'s does not resist.'),
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE, SOLDIER],
      'effectDesc' => clienttranslate('{H} Sacrifice a Character.'),
      'forest' => 4,
      'mountain' => 3,
      'ocean' => 4,
      'costHand' => 3,
      'costReserve' => 3,
      'effectHand' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER, TOKEN],
        'targetLocation' => [STORM_RIGHT, STORM_LEFT],
        'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
      ]),
    ];
  }
}
