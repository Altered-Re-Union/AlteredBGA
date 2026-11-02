<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_Osric extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_130_C',
      'asset' => 'ALT_FUGUE_B_MU_130_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Osric'),
      'typeline' => clienttranslate('Muna Hero'),
      'type' => HERO,
      'thumbnail' => 5,
      'statData' => 34,
      'artist' => 'Ba Vo',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('At Noon — If you\'re first player, create my Signature token: Toot 1/1/1 in your Reserve. (It\'s a Plant <COMPANION> with Reserve Cost {1} and "{R} I gain Anchored.")'),
      'reserveSlots' => 2,
      'landmarkSlots' => 2,
      'signatureToken' => 'MU_Common_Toot',
      'effectPassive' => [
        'Noon' => [
          'listeningConditions' => ['isMe'],
          'condition' => 'isFirstPlayer',
          'output' => [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'MU_Common_Toot', 'targetLocation' => [RESERVE]],
          ],
        ],
      ],
    ];
  }
}
