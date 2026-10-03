<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Common_Zoe extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_130_C',
      'asset' => 'ALT_FUGUE_B_LY_130_C',
      'faction' => FACTION_LY,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Zoe'),
      'typeline' => clienttranslate('Lyra Hero'),
      'type' => HERO,
      'thumbnail' => 5,
      'statData' => 33,
      'artist' => 'Zero Wen',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('When you roll one or more dice — If the result is 6+, exhaust me to create my Signature token in your Reserve. At Noon — If you\'re first player, create my Signature token: Shift 1/1/1 in your Reserve. (It\'s a <COMPANION> token with Reserve Cost {1} and "{r} Roll a die. On a 4+, I gain 1 boost.")'),
      'reserveSlots' => 2,
      'landmarkSlots' => 2,
      'signatureToken' => 'LY_Common_Shift',
      'effectPassive' => [
        'Noon' => [
          'listeningConditions' => ['isMe'],
          'condition' => 'isFirstPlayer',
          'output' => [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'LY_Common_Shift', 'targetLocation' => [RESERVE]],
          ],
        ],
        'RollDie' => [
          'condition' => ['isMe', 'notTapped', 'selectedRoll:6:GTE'],
          'output' => FT::SEQ(
            FT::ACTION(EXHAUST, []),
            FT::ACTION(INVOKE_TOKEN, ['tokenType' => 'LY_Common_Shift', 'targetLocation' => [RESERVE]])
          )
        ]
      ],
    ];
  }
}
