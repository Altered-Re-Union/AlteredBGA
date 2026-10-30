<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_CalypsoBenevolentJailor extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_140_R2',
      'asset' => 'ALT_FUGUE_B_MU_140_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
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
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 2,
      'changedStats' => ['ocean'],
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'subType' => COMPANION,
        'targetLocation' => [STORM_LEFT, STORM_RIGHT],
        'effect' => FT::GAIN(EFFECT, ANCHORED),
      ]),
    ];
  }
}
