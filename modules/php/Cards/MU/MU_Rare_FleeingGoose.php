<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_FleeingGoose extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_131_R2',
      'asset' => 'ALT_FUGUE_B_YZ_131_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Fleeing Goose'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'artist' => 'Zaeliven',
      'extension' => 'NEJ',
      'subtypes' => [ANIMAL],
      'effectDesc' => clienttranslate('When I #leave the Expedition zone# — Another target Character in your Expeditions #or your Reserve# gains 1 boost.'),
      'forest' => 1,
      'mountain' => 0,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['forest'],
      'effectPassive' => [
        'LeaveExpedition' => [
          'output' => FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER, TOKEN],
            'targetLocation' => [...STORMS, RESERVE],
            'excludeSelf' => true,
            'effect' => FT::GAIN(EFFECT, BOOST, 1),
          ]),
        ],
      ],
    ];
  }
}
