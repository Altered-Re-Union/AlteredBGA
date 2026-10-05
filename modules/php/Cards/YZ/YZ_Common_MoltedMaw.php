<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_MoltedMaw extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_148_C',
      'asset' => 'ALT_FUGUE_B_YZ_148_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Molted Maw'),
      'typeline' => clienttranslate('Character - Companion'),
      'type' => CHARACTER,
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'subtypes' => [COMPANION],
      'effectDesc' => clienttranslate('When you sacrifice a Character — I gain 1 boost. (I\'m created in Reserve. You can play me in an Expedition. Remove me from the game if I would go anywhere else.)'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costReserve' => 1,
      'token' => true,
      'effectPassive' => [
        'Discard' => [
          'conditions' => ['isMe', 'isSacrifice:character', 'notDestroyed'],
          'output' => FT::GAIN(ME, BOOST, 1),
        ],
      ],
    ];
  }
}
