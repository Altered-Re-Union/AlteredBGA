<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_BravosSteward extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_141_R2',
      'asset' => 'ALT_FUGUE_B_BR_141_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Bravos Steward'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Atanas Lozenski',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{J} #Up to two# target Characters each gain 1 boost.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'upTo' => true,
        'n' => 2,
        'effect' => FT::GAIN(EFFECT, BOOST),
      ]),
    ];
  }
}
