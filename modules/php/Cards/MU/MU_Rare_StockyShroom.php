<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_StockyShroom extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_134_R1',
      'asset' => 'ALT_FUGUE_B_MU_134_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Stocky Shroom'),
      'typeline' => clienttranslate('Character - Soldier Plant'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Such a bad spore !'),
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, PLANT],
      'effectDesc' => clienttranslate('{J} If you control a <COMPANION>, I gain $<ANCHORED>.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['forest', 'mountain'],
      'effectPlayed' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasCompanionsInExpeditions',
        'effect' => FT::GAIN(ME, ANCHORED),
      ]),
    ];
  }
}
