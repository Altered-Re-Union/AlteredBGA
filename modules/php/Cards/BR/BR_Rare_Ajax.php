<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_Ajax extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_143_R1',
      'asset' => 'ALT_FUGUE_B_BR_143_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ajax'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('When I #leave the Expedition zone# — You may play another Character from your Reserve for {2} less.'),
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 4,
      'costHand' => 4,
      'costReserve' => 4,
      'changedStats' => ['forest', 'mountain', 'costReserve'],
      'effectPassive' => [
        'LeaveExpedition' => [
          'pId' => CONTROLLER,
          'output' => FT::SEQ_OPTIONAL(
            FT::ACTION(SPECIAL_EFFECT, [
              'effect' => 'costReduction',
              'args' => ['type' => CHARACTER, 'reduction' => 2],
            ]),
            FT::ACTION(CHOOSE_ASSIGNMENT, [
              'actions' => ['play'],
              'types' => [CHARACTER],
              'fromLocation' => RESERVE,
              'excludeSelf' => true,
              'mandatory' => true,
            ])
          ),
        ],
      ]
    ];
  }
}
