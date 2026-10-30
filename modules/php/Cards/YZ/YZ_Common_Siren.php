<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_Siren extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_140_C',
      'asset' => 'ALT_FUGUE_B_YZ_140_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Siren'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('{H} Target non-Anchored Character defects. (Joining the Expedition facing it.)'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 2,
      'costHand' => 6,
      'costReserve' => 1,
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'targetLocation' => [...STORMS],
        'excludedStatuses' => [ANCHORED],
        'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect']),
      ]),
    ];
  }
}
