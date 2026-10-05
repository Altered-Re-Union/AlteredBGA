<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Exalted_TheHomer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_146_E',
      'asset' => 'ALT_FUGUE_B_OR_146_E',
      'faction' => FACTION_OD,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('The Homer'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Giovanni Calore',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('When a Character joins your Expeditions — It gains 1 boost.  {J} Create two Ordis Recruit 1/1/1 Soldier tokens, distributed among any Expeditions.'),
      'costHand' => 6,
      'costReserve' => 6,
      'frame' => 2,
      'effectPlayed' => FT::SEQ(
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => STORMS,
        ]),
        FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => STORMS,
          'moreThan1' => true,
        ]),
      ),
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['isCardAddedAnyPlayer:character', 'isStillSameLocation', 'isToStorm', 'isAddedToMyExpedition'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
        'InvokeToken' => [
          'conditions' => ['isCardAddedAnyPlayer:character', 'isStillSameLocation', 'isToStorm', 'isAddedToMyExpedition'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
        'MoveCard' => [
          'conditions' => ['isCardAddedAnyPlayer:character', 'hasSameOwner', 'isStillSameLocation', 'isToStorm', 'isAddedToMyExpedition'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
      ],
    ];
  }
}
