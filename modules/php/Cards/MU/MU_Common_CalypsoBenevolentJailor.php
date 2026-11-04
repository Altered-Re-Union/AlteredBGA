<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_CalypsoBenevolentJailor extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_140_C',
      'asset' => 'ALT_FUGUE_B_MU_140_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Calypso, Benevolent Jailor'),
      'typeline' => clienttranslate('Character - Fairy'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Immortal, patient, and impossible to leave.'),
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'subtypes' => [FAIRY],
      'effectDesc' => clienttranslate('{H} You may target a <COMPANION>. It gains Anchored.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'subType' => COMPANION,
        'targetLocation' => [STORM_LEFT, STORM_RIGHT],
        'effect' => FT::GAIN(EFFECT, ANCHORED),
      ]),
    ];
  }
}
