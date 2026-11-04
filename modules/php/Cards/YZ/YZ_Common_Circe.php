<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_Circe extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_138_C',
      'asset' => 'ALT_FUGUE_B_YZ_138_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Circe'),
      'typeline' => clienttranslate('Character - Mage'),
      'type' => CHARACTER,
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [MAGE],
      'effectDesc' => clienttranslate('{H} Sacrifice a Character. Then, create a Woollyback 1/1/1 Animal token in its Expedition.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'effectHand' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER, TOKEN],
        'effect' => FT::SEQ(
          FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
          FT::ACTION(INVOKE_TOKEN, [
            'tokenType' => 'MU_Common_Woollyback',
            'targetLocation' => ['discardedSource'],
          ]),
        ),
      ])
    ];
  }
}
