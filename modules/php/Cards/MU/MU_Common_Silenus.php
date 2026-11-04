<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_Silenus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_132_C',
      'asset' => 'ALT_FUGUE_B_MU_132_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Silenus'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'artist' => 'Nestor Papatriantafyllou',
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('{H} You may target an $<ANCHORED> Character. It gains 1 boost.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'targetLocation' => [STORM_LEFT, STORM_RIGHT],
        'upTo' => true,
        'statuses' => ANCHORED,
        'effect' => FT::GAIN(TARGET, BOOST),
      ]),
    ];
  }
}
