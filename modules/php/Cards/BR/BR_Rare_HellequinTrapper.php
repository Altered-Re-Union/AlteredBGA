<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_HellequinTrapper extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_139_R2',
      'asset' => 'ALT_FUGUE_B_MU_139_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Hellequin Trapper'),
      'typeline' => clienttranslate('Character - Druid, Animal'),
      'type' => CHARACTER,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'subtypes' => [DRUID, ANIMAL],
      'effectDesc' => clienttranslate('#<TOUGH_CHA_P_1>.#  {J} I gain 2 boosts and Anchored.  At Noon — I lose #1 boost#.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 4,
      'costReserve' => 4,
      'tough' => 1,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, BOOST, 2),
        FT::GAIN(ME, ANCHORED),
      ),
      'effectPassive' => [
        'Noon' => [
          'output' => FT::LOOSE(ME, BOOST, 1),
        ],
      ],
    ];
  }
}
