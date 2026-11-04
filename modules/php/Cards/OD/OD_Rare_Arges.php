<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_Arges extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_135_R2',
      'asset' => 'ALT_FUGUE_B_AX_135_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Arges'),
      'typeline' => clienttranslate('Character - Engineer, Titan'),
      'type' => CHARACTER,
      'artist' => 'Anh Tung',
      'extension' => 'NEJ',
      'subtypes' => [ENGINEER, TITAN],
      'effectDesc' => clienttranslate('$<GIGANTIC>#, <TOUGH_CHA_P_1>.#  I gain 1 boost #per card in your Landmarks.#'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 6,
      'costReserve' => 6,
      'changedStats' => ['forest', 'mountain', 'ocean'],
      'gigantic' => true,
      'tough' => 1,
      'effectPlayed' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXLandmark']),
    ];
  }
}
