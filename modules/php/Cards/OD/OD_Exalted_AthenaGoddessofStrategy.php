<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Exalted_AthenaGoddessofStrategy extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_141_E',
      'asset' => 'ALT_FUGUE_B_OR_141_E',
      'faction' => FACTION_OD,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Athena, Goddess of Strategy'),
      'typeline' => clienttranslate('Character - Soldier Deity'),
      'type' => CHARACTER,
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, DEITY],
      'effectDesc' => clienttranslate('Temple {2}. (You may play me for {2} as a Landmark Permanent - Construction with: "At Noon — You may send me to Reserve.")  At Noon — Create an Ordis Recruit 1/1/1 Soldier token in your Companion Expedition.'),
      'forest' => 4,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'costTemple' => 2,
      'effectPassive' => [
        'Noon' => [
          'childs' => [
            [
              'conditions' => ['isMe', 'isTemple'],
              'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => RESERVE], ['optional' => true]),
            ],
            [
              'conditions' => ['isMe'],
              'output' => FT::ACTION(INVOKE_TOKEN, [
                'targetType' => [CHARACTER],
                'tokenType' => 'OD_Common_OrdisRecruit',
                'targetLocation' => [STORM_RIGHT],
              ]),
            ],
          ],
        ],
      ],
    ];
  }
}
