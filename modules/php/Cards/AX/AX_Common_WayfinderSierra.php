<?php
namespace ALT\Cards\AX;

class AX_Common_WayfinderSierra extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_130_C',
      'asset' => 'ALT_FUGUE_B_AX_130_C',

      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Wayfinder Sierra'),
      'type' => HERO,
      'thumbnail' => 5,
      'statData' => 31,
      'typeline' => clienttranslate('Axiom Hero'),
      'effectDesc' => clienttranslate('At Noon — If you\'re first player, create my Signature token: Oddball 2/2/2 in your Reserve. (It\'s a Robot Companion with Reserve Cost {2} and "I cost {2} less if you played a Construction this Day.")'),
      'reserveSlots' => 2,
      'landmarkSlots' => 3,
      'signatureToken' => 'AX_Common_Oddball',
      'effectPassive' => [
        'Noon' => [
          'listeningConditions' => ['isMe'],
          'condition' => 'isFirstPlayer',
          'output' => [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'AX_Common_Oddball', 'targetLocation' => [RESERVE]],
          ],
        ],
      ],
    ];
  }
}