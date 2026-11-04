<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_Nausicaa extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_132_R2',
      'asset' => 'ALT_FUGUE_B_OR_132_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Nausicaa'),
      'typeline' => clienttranslate('Character - Noble, Soldier'),
      'type' => CHARACTER,
      'artist' => 'Andy Jauffrit',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE, SOLDIER],
      'effectDesc' => clienttranslate('{H} If there\'s #two or more Animals or Companions# among your Reserve #and Expeditions, draw a card#.'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'changedStats' => ['mountain'],
      'effectHand' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasControlInReserveOrExpeditions:animal|companion:2:true',
        'effect' => FT::ACTION(DRAW, ['players' => ME]),
      ]),
    ];
  }
}
