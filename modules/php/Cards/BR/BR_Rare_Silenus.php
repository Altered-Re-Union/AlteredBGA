<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_Silenus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_132_R2',
      'asset' => 'ALT_FUGUE_B_MU_132_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Silenus'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'artist' => 'Nestor Papatriantafyllou',
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('#$<FLEETING> Characters you control are <TOUGH_CHA_P_1>.#  {H} You may target a #<FLEETING># Character. It gains 1 boost.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'changedStats' => ['forest'],
      'dynamicTough' => 'fleeting',
      'effectHand' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER],
        'targetLocation' => [STORM_LEFT, STORM_RIGHT],
        'upTo' => true,
        'statuses' => FLEETING,
        'effect' => FT::GAIN(TARGET, BOOST),
      ]),
    ];
  }
}
