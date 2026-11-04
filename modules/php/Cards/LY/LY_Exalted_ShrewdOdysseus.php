<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Exalted_ShrewdOdysseus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_134_E',
      'asset' => 'ALT_FUGUE_B_LY_134_E',
      'faction' => FACTION_LY,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Shrewd Odysseus'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{H} Put an {V}, {M}, or {O} Terrain Marker on target visible region.  {J} I gain 1 boost per visible single-terrain region.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 4,
      'costReserve' => 4,
      'effectHand' => FT::ACTION(MARK_REGION, [
        'create' => true,
        'regionTypes' => [FOREST, MOUNTAIN, OCEAN],
      ]),
      'effectPlayed' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXMonoVisibleRegions']),
    ];
  }
}
