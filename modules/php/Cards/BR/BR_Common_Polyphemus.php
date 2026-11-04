<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_Polyphemus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_142_C',
      'asset' => 'ALT_FUGUE_B_BR_142_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Polyphemus'),
      'typeline' => clienttranslate('Character - Titan, Rogue'),
      'type' => CHARACTER,
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [TITAN, ROGUE],
      'effectDesc' => clienttranslate('Gigantic.  {J} You may discard a Character from your Reserve to give me 2 boosts.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 6,
      'costReserve' => 6,
      'gigantic' => true,
      'effectPlayed' => FT::ACTION(TARGET, [
        'upTo' => true,
        'targetType' => [CHARACTER],
        'targetLocation' => [RESERVE],
        'targetPlayer' => ME,
        'effect' => FT::SEQ(FT::ACTION(DISCARD, []), FT::GAIN(ME, BOOST, 2)),
      ]),
    ];
  }
}
