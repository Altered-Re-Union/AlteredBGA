<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_HellequinTrapper extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_139_C',
      'asset' => 'ALT_FUGUE_B_MU_139_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Hellequin Trapper'),
      'typeline' => clienttranslate('Character - Druid, Animal'),
      'type' => CHARACTER,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [DRUID, ANIMAL],
      'effectDesc' => clienttranslate('{J} I gain 2 boosts and Anchored.  At Noon — I lose 2 boosts.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 4,
      'costReserve' => 4,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, BOOST, 2),
        FT::GAIN(ME, ANCHORED),
      ),
      'effectPassive' => [
        'Noon' => [
          'output' => FT::LOOSE(ME, BOOST, 2),
        ],
      ],
    ];
  }
}
