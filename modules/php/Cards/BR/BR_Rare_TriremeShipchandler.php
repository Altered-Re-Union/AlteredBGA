<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_TriremeShipchandler extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_136_R2',
      'asset' => 'ALT_FUGUE_B_AX_136_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Trireme Shipchandler'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'flavorText' => clienttranslate('Feeding the crew is a constant concern.'),
      'artist' => 'Julien Carrasco',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('#{J}# Target Character in your Reserve gains #2 boosts#.'),
      'forest' => 2,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['forest'],
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'targetPlayer' => ME,
        'targetLocation' => [RESERVE],
        'effect' => FT::GAIN(EFFECT, BOOST, 2),
      ]),
    ];
  }
}
