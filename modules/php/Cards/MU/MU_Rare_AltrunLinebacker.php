<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_AltrunLinebacker extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_133_R2',
      'asset' => 'ALT_FUGUE_B_BR_133_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Altrun Linebacker'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{H} I gain $<FLEETING> unless there\'s a <COMPANION> in your Expeditions.'),
      'flavorText'  => clienttranslate('"Uju stopped Altrun, and so did I, when I became Atasdi\'s Squire." - Kojo'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain'],
      'effectHand' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasCompanionsInExpeditions',
        'effect' => null,
        'oppositeEffect' => FT::GAIN(ME, FLEETING),
      ]),
    ];
  }
}
