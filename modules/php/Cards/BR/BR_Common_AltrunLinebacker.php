<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_AltrunLinebacker extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_133_C',
      'asset' => 'ALT_FUGUE_B_BR_133_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Altrun Linebacker'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{H} I gain $<FLEETING> unless there\'s a <COMPANION> in your Expeditions.'),
      'flavorText'  => clienttranslate('"Uju stopped Altrun, and so did I, when I became Atasdi\'s Squire." - Kojo'),
      'forest' => 3,
      'mountain' => 1,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'effectHand' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasCompanionsInExpeditions',
        'effect' => null,
        'oppositeEffect' => FT::GAIN(ME, FLEETING),
      ]),
    ];
  }
}
