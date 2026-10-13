<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Common_GulrangTocsin extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_03_C',
      'asset' => 'ALT_CORE_B_OR_03_C',

      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Gulrang & Tocsin'),
      'typeline' => clienttranslate('Ordis Hero'),
      'type' => HERO,
      'thumbnail' => 2,
      'statData' => 13,
      'flavorText' => clienttranslate('The Ordis way lies in patience and resilience.'),
      'artist' => 'Taras Susak',
      'effectDesc' => clienttranslate(
        'When you create a token Character — It gains 1 boost.  If you have less than eight Mana Orbs, <BOOSTED_TKN_P> tokens you control are <DEFENDER>. (Their Expeditions can\'t advance during Dusk.)'
      ),

      'reserveSlots' => 2,
      'landmarkSlots' => 2,

      'effectPassive' => [
        'InvokeToken' => [
          'conditions' => ['isMe', 'isCardPlayed:character'],
          // Apply inline so nested creates (LeaveExpedition / opponent Draw → Quetzalcoatl)
          // still boost before after-finishing resolution moves on. Same pattern as Brassbug Hive.
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
      ],
    ];
  }
}
