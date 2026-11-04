<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_Polyphemus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_142_R2',
      'asset' => 'ALT_FUGUE_B_BR_142_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Polyphemus'),
      'typeline' => clienttranslate('Character - Titan, Rogue'),
      'type' => CHARACTER,
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [TITAN, ROGUE],
      'effectDesc' => clienttranslate('Gigantic,# Tough 1#.  {J} You may discard a Character from your Reserve to give me #3 boosts#.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 6,
      'costReserve' => 6,
      'gigantic' => true,
      'tough' => 1,
      'effectPlayed' => FT::ACTION(TARGET, [
        'upTo' => true,
        'targetType' => [CHARACTER],
        'targetLocation' => [RESERVE],
        'targetPlayer' => ME,
        'effect' => FT::SEQ(FT::ACTION(DISCARD, []), FT::GAIN(ME, BOOST, 3)),
      ]),
    ];
  }
}
