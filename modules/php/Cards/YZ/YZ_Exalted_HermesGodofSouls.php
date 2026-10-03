<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Exalted_HermesGodofSouls extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_136_E',
      'asset' => 'ALT_FUGUE_B_YZ_136_E',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Hermes, God of Souls'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('Temple {2}. (You may play me for {2} as a Landmark Permanent - Construction with: "At Noon — You may send me to Reserve.")  At Noon — If you\'re not first player, create your Hero\'s Signature token in your Reserve, then it gains 1 boost.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 4,
      'costHand' => 3,
      'costReserve' => 3,
      'costTemple' => 2,
      'effectPassive' => [
        'Noon' => [
          'childs' => [
            [
              'conditions' => ['isMe', 'isNotFirstPlayer'],
              'output' => FT::ACTION(INVOKE_TOKEN, [
                'tokenType' => HERO_SIGNATURE,
                'targetLocation' => [RESERVE],
              ]),
            ],
            [
              'conditions' => ['isMe', 'isTemple'],
              'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => RESERVE], ['optional' => true]),
            ],
          ],
        ],
        'InvokeToken' => [
          'listeningConditions' => ['isMe', 'isNoon', 'isNotFirstPlayer'],
          'conditions' => ['isCardAdded:token', 'isToReserve'],
          'output' => FT::GAIN(EFFECT, BOOST, 1),
        ],
      ],
    ];
  }
}
