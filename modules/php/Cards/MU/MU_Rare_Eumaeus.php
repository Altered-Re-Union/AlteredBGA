<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_Eumaeus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_137_R1',
      'asset' => 'ALT_FUGUE_B_MU_137_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Eumaeus'),
      'typeline' => clienttranslate('Character - Druid'),
      'type' => CHARACTER,  
      'flavorText' => clienttranslate('"We still have a long way to go."'),
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [DRUID],
      'effectDesc' => clienttranslate('{J} #Each# Character in your Reserve gains 1 boost.'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['mountain'],
      'effectPlayed' => FT::ACTION(SPECIAL_EFFECT, [
        'targetPlayer' => ME,
        'effect' => 'boostReserve',
        'args' => [],
      ])
    ];
  }
}
