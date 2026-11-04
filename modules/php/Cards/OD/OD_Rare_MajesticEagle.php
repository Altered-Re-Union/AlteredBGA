<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Rare_MajesticEagle extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_131_R1',
      'asset' => 'ALT_FUGUE_B_OR_131_R',
      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Majestic Eagle'),
      'typeline' => clienttranslate('Character - Soldier, Animal'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('It\'s time to spread your wings, take flight, and sail towards the horizon.'),
      'artist' => 'Kevin Sidharta',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, ANIMAL],
      'effectDesc' => clienttranslate('#When I go to your Reserve from your Expeditions — Create an <ORDIS_RECRUIT> Soldier token in my Expedition.#'),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 1,
      'effectPassive' => [
        'LeaveExpedition' => [
          'pId' => CONTROLLER,
          'condition' => 'isToReserve',
          'output' => FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => ['source'],
          ]),
        ],
      ]
    ];
  }
}
