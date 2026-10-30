<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_SquireKojo extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_130_C',
      'asset' => 'ALT_FUGUE_B_BR_130_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Squire Kojo'),
      'typeline' => clienttranslate('Bravos Hero'),
      'type' => HERO,
      'thumbnail' => 5,
      'statData' => 32,
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('At Noon — If you\'re first player, create my Signature token: <BLAZING_BOODA> in your Reserve. (It\'s a <COMPANION> with Reserve Cost {2}.)'),
      'reserveSlots' => 2,
      'landmarkSlots' => 2,
      'signatureToken' => 'BR_Common_BlazingBooda',
      'effectPassive' => [
        'Noon' => [
          'listeningConditions' => ['isMe'],
          'condition' => 'isFirstPlayer',
          'output' => [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'BR_Common_BlazingBooda', 'targetLocation' => [RESERVE]],
          ],
        ],
      ],
    ];
  }
}
