<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_BravosSteward extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_141_C',
      'asset' => 'ALT_FUGUE_B_BR_141_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Bravos Steward'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Atanas Lozenski',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{J} Target Character gains 1 boost.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'effect' => FT::GAIN(EFFECT, BOOST),
      ]),
    ];
  }
}
