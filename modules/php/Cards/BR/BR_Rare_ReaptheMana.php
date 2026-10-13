<?php

namespace ALT\Cards\BR;

use ALT\Helpers\FT;

class BR_Rare_ReaptheMana extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_BR_121_R1',
      'asset'  => 'ALT_EOLE_B_BR_121_R',

      'faction'  => FACTION_BR,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Reap the Mana"),
      'typeline' => clienttranslate("Landmark_permanent - Feat"),
      'type'  => PERMANENT,
      'flavorText'  => clienttranslate('After Kojo, she\'s the second to have managed this feat.'),
      'artist' => "Zero Wen",
      'extension' => 'ROC',
      'subtypes'  => [FEAT, LANDMARK],
      'effectDesc' => clienttranslate('{J} You may put a card from your Reserve in your Mana zone, exhausted. #If you do, <RESUPPLY_LOW>.#  When you pass — If there are twelve or more Mana Orbs in your Mana zone, complete me.'),
      'supportDesc' => clienttranslate('<COMPLETED_LOW>: If one of your Expeditions would move forward during Dusk, you may exhaust me ({T}) to make it move forward one more region instead.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetLocation' => [RESERVE],
        'targetType' => [TOKEN, CHARACTER, PERMANENT, SPELL],
        'targetPlayer' => ME,
        'upTo' => true,
        'effect' => FT::SEQ(
          FT::ACTION(DISCARD, [
            'destination' => MANA,
            'tapped' => true,
            'force' => true,
          ]),
          FT::ACTION(RESUPPLY, []),
        ),
      ]),
      'effectPassive' => [
        'EndTurn' => [
          'conditions' => ['isMe', 'hasXMana:12', 'isThisFeatIncomplete'],
          'output' => FT::ACTION(COMPLETE_FEAT, ['cardId' => 'source']),
        ],        
        'AfterDusk' => [
          'conditions' => ['isMe', 'isThisFeatCompleted', 'notTapped', 'anyOfMyExpeditionsHasMoved'],
          'output' => FT::SEQ_OPTIONAL(
            FT::ACTION(TAP, []),
            FT::ACTION(TARGET_EXPEDITION, [
              'players' => ME,
              'type' => 'movedThisDusk',
              'effect' => FT::ACTION(MOVE_EXPEDITION, [
                'pId' => ME,
                'n' => 1,
                'force' => true,
              ]),
            ]),
          ),
        ],
      ],
    ];
  }
}
