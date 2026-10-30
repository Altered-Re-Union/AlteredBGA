<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_TriremeBoatswain extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_132_C',
      'asset' => 'ALT_FUGUE_B_AX_132_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Trireme Boatswain'),
      'typeline' => clienttranslate('Character - Engineer'),
      'flavorText'  => clienttranslate('The journey was anything but easy.'),
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [ENGINEER],
      'effectDesc' => clienttranslate('{J} If there are two or more cards in your Reserve, I gain 1 boost.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'checkReserveCards:2',
        'effect' => FT::GAIN(ME, BOOST),
      ]),
    ];
  }
}
