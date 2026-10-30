<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_Silenus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_132_R1',
      'asset' => 'ALT_FUGUE_B_MU_132_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Silenus'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'artist' => 'Nestor Papatriantafyllou',
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('#$<ANCHORED> Characters you control are <TOUGH_CHA_P_1>.#  {H} You may target an $<ANCHORED> Character. It gains 1 boost.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'changedStats' => ['forest'],
      'dynamicTough' => 'anchored',
      'effectHand' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER],
        'targetLocation' => [STORM_LEFT, STORM_RIGHT],
        'upTo' => true,
        'statuses' => ANCHORED,
        'effect' => FT::GAIN(TARGET, BOOST),
      ]),
    ];
  }
}
