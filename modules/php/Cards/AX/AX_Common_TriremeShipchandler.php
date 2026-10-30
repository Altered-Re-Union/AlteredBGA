<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_TriremeShipchandler extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_136_C',
      'asset' => 'ALT_FUGUE_B_AX_136_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Trireme Shipchandler'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'flavorText' => clienttranslate('Feeding the crew is a constant concern.'),
      'artist' => 'Julien Carrasco',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{R} Target Character in your Reserve gains 1 boost.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'effectReserve' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'targetPlayer' => ME,
        'targetLocation' => [RESERVE],
        'effect' => FT::GAIN(EFFECT, BOOST, 1),
      ]),
    ];
  }
}
