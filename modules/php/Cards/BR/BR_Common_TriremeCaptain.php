<?php
namespace ALT\Cards\BR;

class BR_Common_TriremeCaptain extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_135_C',
      'asset' => 'ALT_FUGUE_B_BR_135_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Trireme Captain'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('Companions in your Expeditions are <TOUGH_CHA_P_1>. (Opponents must pay {1} to target them.)'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'dynamicTough' => 'universalCharacter1',
      'universalToughScope' => 'expeditionCompanion',
    ];
  }
}
