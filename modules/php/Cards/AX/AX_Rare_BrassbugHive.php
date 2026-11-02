<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_BrassbugHive extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_30_R1',
      'asset' => 'ALT_CORE_B_AX_30_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Brassbug Hive'),
      'typeline' => clienttranslate('Permanent - Landmark'),
      'type' => PERMANENT,
      'subtypes' => [LANDMARK],
      'flavorText' => clienttranslate('What could possibly go wrong with an adorable little self-replicating autonomous robot?'),
      'effectDesc' => clienttranslate(
        '{J} Create a <BRASSBUG> Robot token in target Expedition.  At Noon — Create a <BRASSBUG> Robot token in target Expedition.  #When a Robot joins your Expeditions — It gains 1 boost.#'
      ),
      'artist' => 'HuoMiao Studio',

      'costHand' => 6,
      'costReserve' => 6,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::ACTION(INVOKE_TOKEN, [
        'pId' => 'source',
        'tokenType' => 'AX_Common_Brassbug',
        'targetLocation' => STORMS,
      ]),
      'effectPassive' => [
        'Noon' => [
          'condition' => 'isMe',
          'output' => FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'AX_Common_Brassbug',
            'targetLocation' => STORMS,
          ]),
        ],
        'ChooseAssignment' => [
          'conditions' => ['isCardAdded:robot', 'isStillSameLocation', 'hasSameOwner'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
        'InvokeToken' => [
          'conditions' => ['isCardAdded:robot', 'isStillSameLocation', 'hasSameOwner'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
      ],
    ];
  }
}
