<?php
namespace ALT\Cards\OD;

class OD_Common_Eyota extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_130_C',
      'asset' => 'ALT_FUGUE_B_OR_130_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Eyota'),
      'typeline' => clienttranslate('Ordis Hero'),
      'type' => HERO,
      'thumbnail' => 5,
      'statData' => 35,
      'artist' => 'Tristan Bideau',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('At Noon — If you\'re first player, create my Signature token: Echo 1/1/1 in your Reserve. (It\'s a Soldier <COMPANION> with Reserve Cost {1} and "{R} If you control two or more other Soldiers, I gain 1 boost.")'),
      'reserveSlots' => 2,
      'landmarkSlots' => 2,
      'signatureToken' => 'OD_Common_Echo',
      'effectPassive' => [
        'Noon' => [
          'listeningConditions' => ['isMe'],
          'condition' => 'isFirstPlayer',
          'output' => [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'OD_Common_Echo', 'targetLocation' => [RESERVE]],
          ],
        ],
      ],
    ];
  }
}
