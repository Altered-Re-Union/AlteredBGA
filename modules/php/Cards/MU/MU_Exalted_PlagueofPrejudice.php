<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Exalted_PlagueofPrejudice extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_MU_115_E',
      'asset'  => 'ALT_EOLE_B_MU_115_E',
      'faction'  => FACTION_MU,
      'rarity'  => RARITY_EXALTED,
      'name'  => clienttranslate("Plague of Prejudice"),
      'typeline' => clienttranslate("Character - Corruption"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('Injustice is fertile ground for evil.'),
      'artist' => "Taras Susak",
      'extension' => 'ROC',
      'subtypes'  => [CORRUPTION],
      'effectDesc' => clienttranslate('{J} I gain <ANCHORED>.  {H} Send to Reserve each other Character with {V} less than or equal to mine.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 5,
      'costReserve' => 2,
      'effectPlayed' => FT::GAIN(ME, ANCHORED),
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'excludeSelf' => true,
        'ignoreTough' => true,
        'n' => INFTY,
        'compareTargetBiome' => ['biome' => FOREST, 'op' => 'lte', 'source' => 'source'],
        'effect' => FT::DISCARD_TO_RESERVE(),
      ]),
    ];
  }
}
