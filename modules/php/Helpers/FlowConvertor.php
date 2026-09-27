<?php

namespace ALT\Helpers;

use ALT\Core\Globals;
use ALT\Managers\Players;

// Allow to use a short flow description syntax
abstract class FlowConvertor
{
  public static function getTriggers()
  {
    return [
      1 => ['description' => clienttranslate('{R}'), 'trigger' => '', 'type' => 'effectReserve'],
      2 => [
        'description' => clienttranslate('When an opponent draws one or more cards or does [RESUPPLY_T] —'),
        'trigger' => ['Draw', 'Resupply', 'Morning'],
        'condition' => ['isOpponentDraw', 'realResupply'],
      ],
      4 => [
        'description' => clienttranslate('When one of your Expeditions moves forward due to {V} —'),
        'trigger' => 'AfterDusk',
        'condition' => 'movesStormsWithForest',
        'n' => 'eachExpedition',
      ], // to check if bug with Rin
      5 => [
        'description' => clienttranslate('When a Robot joins your Expeditions —'),
        'trigger' => ['ChooseAssignment', 'InvokeToken'],
        'condition' => ['isCardPlayed:robot:::true'],
      ],
      7 => [
        'description' => clienttranslate('When I go to Reserve from your hand —'),
        'trigger' => 'Discard',
        'condition' => 'isMyselfDiscarded:hand:reserve',
      ],
      8 => ['description' => clienttranslate('When I\'m sacrificed —'), 'trigger' => 'Discard', 'condition' => 'isSacrificed'],
      10 => [
        'description' => clienttranslate('When you play a Permanent with Hand Cost {3} or more —'),
        'trigger' => 'ChooseAssignment',
        'condition' => 'isCardPlayed:permanent:3',
      ],
      11 => [
        'description' => clienttranslate('When I go to Reserve from the Expedition zone —'),
        'trigger' => 'LeaveExpedition',
        'condition' => ['notFleeting', 'notDiscarded'],
      ],
      12 => [
        'description' => clienttranslate('When I leave the Expedition zone —'),
        'trigger' => 'LeaveExpedition',
        'pId' => CONTROLLER,
      ],
      13 => [
        'description' => clienttranslate('When a Character you control gains 1 or more boosts —'),
        'trigger' => 'Gain',
        'condition' => 'isCharacterBoostedAndUntap',
      ], // condition to check
      14 => [
        'description' => clienttranslate('When my Expedition fails to move forward during Dusk — After Rest:'),
        'trigger' => 'AfterDusk',
        'condition' => 'myExpeditionHasNotMoved',
        'afterRest' => true,
      ],
      15 => [
        'description' => clienttranslate('When you play another Character with a base statistic of 0 —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isCardPlayed::::true', 'isCardPlayedWithZeroStat'],
      ],
      16 => [
        'description' => clienttranslate('When you play a Permanent —'),
        'trigger' => 'ChooseAssignment',
        'condition' => 'isCardPlayed:permanent',
      ],
      17 => ['description' => clienttranslate('At Dusk —'), 'trigger' => 'AtDusk'],
      19 => [
        'description' => clienttranslate('When another non-token Character joins your Expeditions —'),
        'trigger' => 'ChooseAssignment',
        'condition' => 'isCardPlayed:characterOnly:::true',
      ],
      20 => ['description' => clienttranslate('At Noon —'), 'trigger' => 'Noon', 'condition' => 'isMe'],
      21 => [
        'description' => clienttranslate('When another Character joins your Expeditions —'),
        'trigger' => ['ChooseAssignment', 'InvokeToken', 'MoveCard'],
        'condition' => ['isCardAddedAnyPlayer:character:::true', 'isStillSameLocation', 'hasSameOwner'],
      ],
      22 => ['description' => clienttranslate('{H}'), 'trigger' => '', 'type' => 'effectHand'],
      23 => ['description' => clienttranslate('[]]')],
      24 => ['description' => clienttranslate('{J}'), 'trigger' => '', 'type' => 'effectPlayed'],
      25 => ['description' => clienttranslate('When you create a token —'), 'trigger' => 'InvokeToken', 'condition' => 'isMe'],
      26 => ['description' => clienttranslate('When you roll one or more dice —'), 'trigger' => 'RollDie', 'condition' => 'isMe'],
      27 => [
        'description' => clienttranslate('When you play a Spell —'),
        'trigger' => 'ChooseAssignment',
        'condition' => 'isCardPlayed:spell',
      ],
      28 => [
        'description' => clienttranslate('When a card leaves your Reserve during the Afternoon —'),
        'trigger' => ['ChooseAssignment', 'Discard'],
        'condition' => ['isAfternoon', 'hasSameOwner', 'isFromReserve', 'excludeSelf'],
      ],
      192 => ['description' => clienttranslate('{D}'), 'trigger' => '', 'type' => 'effectSupport'],
      231 => ['description' => clienttranslate('When I\'m sacrificed —'), 'trigger' => 'Discard', 'condition' => 'isSacrificed'],
      236 => [
        'description' => clienttranslate('When my Expedition fails to move forward during Dusk — After Rest:'),
        'trigger' => 'AfterDusk',
        'condition' => 'myExpeditionHasNotMoved',
        'afterRest' => true,
      ],
      239 => [
        'description' => clienttranslate('When an opponent draws one or more cards or does [RESUPPLY_T] —'),
        'trigger' => ['Draw', 'Resupply', 'Morning'],
        'condition' => 'isOpponentDraw',
      ],
      240 => [
        'description' => clienttranslate('When I gain 1 or more boosts —'),
        'trigger' => 'Gain',
        'condition' => 'hasGainedBoost',
      ],
      // Alizé
      250 => [
        'description' => clienttranslate('When a card goes from your hand or deck to Reserve —'),
        'trigger' => ['Discard', 'Resupply'],
        'condition' => ['hasSameOwner', 'isDiscarded:hand:reserve', 'excludeSelf'],
      ],
      253 => [
        'description' => clienttranslate('When another Character joins my Expedition —'),
        'trigger' => ['ChooseAssignment', 'InvokeToken'],
        'condition' => ['isCardAddedAnyPlayer:character', 'isPlayedInSameLocation', 'excludeSelf'],
      ],
      254 => [
        'description' => clienttranslate('When another Character you control gains <FLEETING> —'),
        'trigger' => ['Gain'],
        'condition' => ['isControlledCharacterGain', 'isGain:fleeting', 'excludeSelf'],
      ],
      419 => [
        'description' => clienttranslate('When another Character joins my Expedition —'),
        'trigger' => ['ChooseAssignment', 'InvokeToken'],
        'condition' => ['isCardAdded:character', 'isPlayedInSameLocation', 'excludeSelf'],
      ],
      263 => [
        'description' => clienttranslate('When another non-token Character joins one of your Expeditions that is behind —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isCardAdded:characterOnly', 'cardPlayedExpeditionIsBehind', 'excludeSelf'],
      ],
      256 => ['description' => clienttranslate('When I gain <ASLEEP> —'), 'trigger' => 'Gain', 'condition' => 'hasGained:asleep'],
      257 => [
        'description' => clienttranslate('When I gain <FLEETING> —'),
        'trigger' => 'Gain',
        'condition' => 'hasGained:fleeting',
      ],
      372 => [
        'description' => clienttranslate('When I leave the Expedition zone, if I was <FLEETING> —'),
        'trigger' => 'LeaveExpedition',
        'condition' => 'hasFleeting',
        'pId' => CONTROLLER,
      ],
      258 => [
        'description' => clienttranslate('When my Expedition moves forward —'),
        'trigger' => ['AfterDusk', 'MoveExpedition'],
        'condition' => 'myExpeditionHasMoved',
      ],
      259 => [
        'description' => clienttranslate('When my Expedition moves forward due to {V} —'),
        'trigger' => 'AfterDusk',
        'condition' => 'movesStormsWithForest',
      ],
      260 => [
        'description' => clienttranslate('When you exhaust a card in Reserve —'),
        'trigger' => 'Exhaust',
        'condition' => ['isMe', 'isExhaustedInLocation:reserve'],
      ],
      261 => [
        'description' => clienttranslate('When you play another Character in {V} —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isMe', 'isCardAdded:character:::true', 'isPlayedCardInBiome:forest', 'excludeSelf'],
      ],
      // Bise
      18 => ['description' => clienttranslate('At Night —'), 'trigger' => 'AfterDusk'],
      432 => ['description' => clienttranslate('{I}'), 'type' => 'effectInfinity'],
      433 => [
        'description' => clienttranslate('{I} At Noon —'),
        'trigger' => 'Noon',
        'type' => 'effectInfinity',
        'condition' => 'isMe',
      ],
      434 => [
        'description' => clienttranslate('{I} When an opponent draws one or more cards —'),
        'trigger' => ['Draw', 'Resupply', 'Morning'],
        'condition' => 'isOpponentDraw',
        'type' => 'effectInfinity',
      ],
      517 => [
        'description' => clienttranslate('{I} When another Character joins your Expeditions —'),
        'trigger' => ['ChooseAssignment', 'InvokeToken', 'MoveCard'],
        'condition' => 'isCardAddedAnyPlayer:character:::true',
        'type' => 'effectInfinity',
      ],
      435 => [
        'description' => clienttranslate('{I} When you play a Spell —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isCardPlayed:spell', 'excludeSelf'],
        'type' => 'effectInfinity',
      ],
      436 => [
        'description' => clienttranslate('{I} When you play another Character —'),
        'trigger' => 'ChooseAssignment',
        'condition' => 'isCardPlayed:character:::true',
        'type' => 'effectInfinity',
      ],
      437 => [
        'description' => clienttranslate('{I} When you play another Character with a base statistic of 0 —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isCardPlayedWithZeroStat', 'excludeSelf'],
        'type' => 'effectInfinity',
      ],
      438 => [
        'description' => clienttranslate('{I} When you roll one or more dice —'),
        'trigger' => 'RollDie',
        'condition' => 'isMe',
        'type' => 'effectInfinity',
      ],
      439 => [
        'description' => clienttranslate('{I} When you sacrifice a Character —'),
        'trigger' => 'Discard',
        'condition' => ['isMe', 'isSacrifice:character'],
        'type' => 'effectInfinity',
      ],
      519 => ['description' => '•', 'type' => 'manInTheMazeUnique'],
      440 => [
        'description' => clienttranslate('When a card goes to the discard pile —'),
        'trigger' => 'Discard',
        'condition' => 'isDiscarded::discard',
      ],
      441 => [
        'description' => clienttranslate('When a Character in your Reserve gains 1 or more boosts —'),
        'trigger' => 'Gain',
        'condition' => ['isMyGainInReserve', 'isGain:boost', 'isGainCardType:character'],
      ],
      442 => [
        'description' => clienttranslate('When a Character you control gains <ANCHORED> —'),
        'trigger' => 'Gain',
        'condition' => 'hasPlayerGained:anchored',
      ],
      443 => [
        'description' => clienttranslate('When a Character you control gains <FLEETING> —'),
        'trigger' => 'Gain',
        'condition' => 'hasGainedFleeting',
      ],
      444 => [
        'description' => clienttranslate('When an opponent plays a card from Reserve —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isNotMe', 'isFromReserve', 'isAddedCardOpponentEvent'],
      ],
      445 => [
        'description' => clienttranslate('When an opponent plays a Character —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isNotMe', 'isAddedCardOpponentEvent:character'],
      ],
      446 => [
        'description' => clienttranslate('When I leave the Expedition zone, for each of my boosts —'),
        'trigger' => 'LeaveExpedition',
        'condition' => 'hasBoost',
        'pId' => CONTROLLER,
      ],
      526 => [
        'description' => clienttranslate('When I leave the Expedition zone, if I was <BOOSTED> —'),
        'trigger' => 'LeaveExpedition',
        'condition' => 'hasBoost',
        'pId' => CONTROLLER,
      ],
      447 => [
        'description' => clienttranslate('When you play another card from Reserve —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isMe', 'excludeSelf', 'isFromReserve', 'isCardAdded'],
      ],
      448 => [
        'description' => clienttranslate('When you sacrifice a Character —'),
        'trigger' => 'Discard',
        'condition' => ['isMe', 'isSacrifice:character'],
      ],
      532 => [
        'description' => clienttranslate('{I} At Noon —'),
        'trigger' => 'Noon',
        'type' => 'effectInfinity',
        'condition' => 'isMe',
      ],
      540 => [
        'description' => clienttranslate('When I leave the Expedition zone, if I was <BOOSTED> —'),
        'trigger' => 'LeaveExpedition',
        'condition' => 'hasBoost',
        'pId' => CONTROLLER,
      ],
      9 => [
        'description' => clienttranslate('When a non-<BOOSTED> Character you control gains 1 or more boosts —'),
        'trigger' => ['Gain'],
        'condition' => 'GainFirstBoost',
      ],
      // Patch note 13/05/2025
      675 => [
        'description' => clienttranslate('When a non-Token Character you control gains 1 or more boosts —'),
        'trigger' => 'Gain',
        'condition' => ['isNonTokenBoostedAndUntap'],
      ], // condition to check
      // Cyclone
      542 => [
        'description' => clienttranslate('When a Character joins the Expedition facing me —'),
        'trigger' => ['ChooseAssignment', 'InvokeToken', 'MoveCard', 'EatMeEnergyBars'],
        'listeningConditions' => ['isAddedCardAnyPlayer:character', 'isPlayedInOpponentExpedition'],
        'condition' => ['isSourceSameLocation'],
      ],
      543 => [
        'description' => clienttranslate('When my Expedition fails to move forward during Dusk —'),
        'trigger' => 'AfterDusk',
        'condition' => ['myExpeditionHasNotMoved'],
      ],
      544 => [
        'description' => clienttranslate('When my Expedition fails to move forward during Dusk —'),
        'trigger' => 'AfterDusk',
        'condition' => ['myExpeditionHasNotMoved'],
      ],
      674 => [
        'description' => clienttranslate('When my Expedition moves forward —'),
        'trigger' => ['AfterDusk', 'MoveExpedition'],
        'condition' => ['myExpeditionHasMoved'],
      ],
      656 => ['description' => clienttranslate('When you create one or more tokens —'), 'trigger' => 'InvokeTokenOnce', 'condition' => ['isMe']],
      545 => [
        'description' => clienttranslate('When you pass first —'),
        'trigger' => 'EndTurn',
        'condition' => ['isFirstPassing', 'isMe'],
      ],
      546 => [
        'description' => clienttranslate('When you pass first —'),
        'trigger' => 'EndTurn',
        'condition' => ['isFirstPassing', 'isMe'],
      ],
      547 => [
        'description' => clienttranslate('When you play a Spell with Base Cost {4} or more —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['notTapped', 'isCardPlayed:spell', 'cardPlayedCostCheck:4'],
      ],
      549 => [
        'description' => clienttranslate('When you sacrifice a Character or Permanent —'),
        'trigger' => 'Discard',
        'condition' => ['isMe', 'isSacrificeCharacterPermanent'],
      ],
      548 => [
        'description' => clienttranslate('When you sacrifice a Permanent —'),
        'trigger' => 'Discard',
        'condition' => ['isMe', 'isSacrifice:permanent'],
      ],
      688 => [
        'description' => clienttranslate('When an opponent receives a <GIFT> —'),
        'trigger' => ['InvokeToken', 'Draw', 'Resupply'],
        'listeningConditions' => ['isMyTurn', 'isAfternoon', 'isNotMe', 'notTapped'],
      ],
      689 => [
        'description' => clienttranslate('When an opponent receives a <GIFT> —'),
        'trigger' => ['InvokeToken', 'Draw', 'Resupply'],
        'listeningConditions' => ['isMyTurn', 'isAfternoon', 'isNotMe', 'notTapped'],
      ],
      780 => [
        'description' => clienttranslate('When you play a Permanent with Base Cost {4} or more —'),
        'trigger' => 'ChooseAssigment',
        'condition' => ['isCardPlayed:permanent', 'cardPlayedCostCheck:4'],
      ],
      781 => [
        'description' => clienttranslate('When you <RESUPPLY_LOW> —'),
        'trigger' => 'Resupply',
        'condition' => ['isMe'],
      ],
      794 => [
        'description' => clienttranslate('When you pass —'),
        'trigger' => 'EndTurn',
        'condition' => ['isMe'],
      ],
      // EOLE triggers
      797 => [
        'description' => clienttranslate('When a {D} ability is activated on your turn —'),
        'trigger' => 'ChooseAssignment',
        'condition' => ['isMe', 'isMyTurn', 'isSupportEffect'],
      ],
      798 => [
        'description' => clienttranslate('When you discard a card from your hand —'),
        'trigger' => 'Discard',
        'condition' => ['isMe', 'isDiscarded:hand:discard', 'excludeSelf'],
      ],
      801 => [
        'description' => clienttranslate('When an Animal other than me joins your Expeditions —'),
        'trigger' => ['ChooseAssignment', 'InvokeToken', 'MoveCard'],
        'condition' => ['isCardAddedAnyPlayer:animal:::true', 'isStillSameLocation', 'hasSameOwner', 'excludeSelf'],
      ],
      804 => [
        'description' => clienttranslate('When you put a card from your hand in Reserve —'),
        'trigger' => 'Discard',
        'condition' => ['isMe', 'isInStorms', 'isDiscarded:hand:reserve'],
      ],
      805 => [
        'description' => clienttranslate('When I leave an <ASCENDED_S> Expedition —'),
        'trigger' => 'LeaveExpedition',
        'condition' => ['wasCardExpeditionAscended'],
        'pId' => CONTROLLER,
      ],
      808 => [
        'description' => clienttranslate('When my Expedition moves forward <DUE_TO_ASCENSION> —'),
        'trigger' => ['AfterDusk', 'MoveExpedition'],
        'condition' => ['movesStormDueToAscension'],
      ],
      810 => [
        'description' => clienttranslate('When one or more of your <ASCENDED_P> Expeditions moves forward —'),
        'trigger' => ['AfterDusk', 'MoveExpedition'],
        'condition' => ['movesAscendedAnyExpeditions'],
      ],
      937 => ['description' => clienttranslate('{R}'), 'trigger' => '', 'type' => 'effectReserve'],
    ];
  }

  public static function getConditions()
  {
    return [
      166 => [
        'description' => clienttranslate('If you control two or more Plants other than me:'),
        'condition' => 'hasControl:plant:2:true',
      ],
      167 => [
        'description' => clienttranslate('If you have three or more base statistics of 0 among Characters you control:'),
        'condition' => 'has3WithZeroStat',
      ],
      168 => ['description' => clienttranslate('If I have 3 or more boosts:'), 'condition' => 'hasBoost:3'],
      169 => [
        'description' => clienttranslate('If you control two or more [BOOSTED_CHA_P] Characters:'),
        'condition' => 'hasControl::2::boosted',
      ],
      170 => [
        'description' => clienttranslate('You may discard one of your Mana Orbs. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [MANA],
          'upTo' => true,
          'targetType' => [CHARACTER, TOKEN, SPELL, PERMANENT],
          'effect' => FT::SEQ(FT::ACTION(DISCARD, []), 'OUTPUT'),
        ]),
        'ifYouDo' => true,
      ],
      171 => [
        'description' => clienttranslate('You may put a card from your hand in Reserve. If it\'s a Permanent:'),
        'effect' => FT::ACTION(
          TARGET,
          [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'upTo' => true,
            'targetLocation' => [HAND],
            'effect' => FT::SEQ(
              FT::DISCARD_TO_RESERVE(),
              FT::ACTION(CHECK_CONDITION, [
                'conditions' => ['isPermanentFromTarget'],
                'effect' => 'OUTPUT',
                'oppositeEffect' => 'OPPOSITE',
              ])
            ),
          ],
          ['optional' => true]
        ),
        // 'passiveEffect' => ['Discard' => ['condition' => ['isSource', 'isDiscarded:hand:reserve:permanent'], 'output' => 'OUTPUT']], // to check
      ],
      172 => [
        'description' => clienttranslate('You may put a card from your hand in Reserve. If it\'s a Spell:'),
        'effect' => FT::ACTION(
          TARGET,
          [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'upTo' => true,
            'targetLocation' => [HAND],
            'effect' => FT::SEQ(
              FT::DISCARD_TO_RESERVE(),
              FT::ACTION(CHECK_CONDITION, [
                'conditions' => ['isSpellFromTarget'],
                'effect' => 'OUTPUT',
                // 'description' => clienttranslate('if it\'s a spell'),
                'oppositeEffect' => 'OPPOSITE',
              ])
            ),
          ],
          ['optional' => true]
        ),
        // 'passiveEffect' => ['Discard' => ['condition' => ['isSource', 'isDiscarded:hand:reserve:spell'], 'output' => 'OUTPUT']], // to check
      ],
      173 => ['description' => clienttranslate('If you control four or more Characters:'), 'condition' => 'hasControl::4'],
      175 => [
        'description' => clienttranslate('You may sacrifice a Permanent. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [PERMANENT],
          'upTo' => true,
          'effect' => FT::SEQ(FT::ACTION(DISCARD, ['desc' => 'sacrifice']), 'OUTPUT'),
        ]),
        'ifYouDo' => true,
      ],
      176 => ['description' => clienttranslate('If you control two or more Landmarks:'), 'condition' => 'hasControl:landmark:2'],
      177 => [
        'description' => clienttranslate('Roll a die. On a 4+:'),
        'effect' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '4+' => 'OUTPUT',
            '1-3' => 'OPPOSITE',
          ],
        ]),
      ],
      178 => [
        'description' => clienttranslate('You may sacrifice a Character. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [TOKEN, CHARACTER],
          'upTo' => true,
          'effect' => FT::SEQ(FT::ACTION(DISCARD, ['desc' => 'sacrifice']), 'OUTPUT'),
        ]),
        'ifYouDo' => true,
      ],
      179 => ['description' => clienttranslate('If you control three or more Characters:'), 'condition' => 'hasControl::3'],
      180 => [
        'description' => clienttranslate('You may sacrifice a Character or Permanent. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [TOKEN, CHARACTER, PERMANENT],
          'upTo' => true,
          'effect' => FT::SEQ(FT::ACTION(DISCARD, ['desc' => 'sacrifice']), 'OUTPUT'),
        ]),
        'ifYouDo' => true,
      ],
      181 => ['description' => clienttranslate('If I have 1 or more boosts:'), 'condition' => 'hasBoost'],
      182 => [
        'description' => clienttranslate('You may put a card from your hand in Reserve. If you do:'),
        'effect' => FT::ACTION(
          TARGET,
          [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'upTo' => true,
            'targetLocation' => [HAND],
            'effect' => FT::SEQ(FT::DISCARD_TO_RESERVE(), 'OUTPUT'),
          ],
          ['optional' => true]
        ),
        'ifYouDo' => true,
      ],
      183 => ['description' => clienttranslate('If I have 2 or more boosts:'), 'condition' => 'hasBoost:2'],
      186 => [
        'description' => clienttranslate('You may pay {1}. If you do:'),
        'effect' => FT::SEQ_OPTIONAL(FT::ACTION(PAY, ['pay' => 1]), 'OUTPUT'),
        'optional' => true,
        'ifYouDo' => true,
      ],
      187 => [
        'description' => clienttranslate('You may discard a card from your Reserve. If you do:'),
        'effect' => FT::ACTION(
          TARGET,
          [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::SEQ(FT::ACTION(DISCARD, []), 'OUTPUT'),
          ],
          ['optional' => true]
        ),
        'ifYouDo' => true,
      ],
      188 => ['description' => clienttranslate('If you control a token:'), 'condition' => 'hasControl:token:1'],
      189 => ['description' => clienttranslate('If you control one or more Landmarks:'), 'condition' => 'hasControl:landmark:1'],
      190 => ['description' => clienttranslate('If I\'m not <FLEETING>:'), 'condition' => 'notFleeting'],
      191 => ['description' => clienttranslate('[]]')],
      198 => ['description' => clienttranslate('If you have less than eight Mana Orbs:'), 'condition' => 'hasLess8Mana'],
      201 => [
        'description' => clienttranslate('Unless you control two or more Plants other than me:'),
        'condition' => 'hasControl:plant:1:true::LTE',
      ],
      202 => [
        'description' => clienttranslate('Unless you control two or more Bureaucrats other than me:'),
        'condition' => 'hasControl:bureaucrat:1:true::LTE',
      ],
      247 => [
        'description' => clienttranslate('You may sacrifice me. If you do:'),
        'effect' => FT::SEQ_OPTIONAL(FT::ACTION(DISCARD, ['desc' => 'sacrifice', 'cardId' => ME]), 'OUTPUT'),
        'ifYouDo' => true,
      ],
      // Alizé
      354 => [
        'description' => clienttranslate('If each of your Expeditions is behind or tied:'),
        'condition' => 'allExpeditionsAreBehindOrTied',
      ],
      426 => ['description' => clienttranslate('If I\'m in {M}:'), 'condition' => 'isInBiome:mountain:true'],
      352 => ['description' => clienttranslate('If I\'m in {V}:'), 'condition' => 'isInBiome:forest:true'],
      405 => ['description' => clienttranslate('If I\'m in {V}:'), 'condition' => 'isInBiome:forest:true'],
      353 => [
        'description' => clienttranslate('If I\'m the only Character in my Expedition:'),
        'condition' => 'XCharacterInExpedition:1:E',
      ],
      378 => [
        'description' => clienttranslate('If I\'m the only Character in my Expedition:'),
        'condition' => 'XCharacterInExpedition:1:E',
      ],
      423 => [
        'description' => clienttranslate('If I\'m the only Character in my Expedition:'),
        'condition' => 'XCharacterInExpedition:1:E',
      ],
      355 => ['description' => clienttranslate('If my Expedition is behind:'), 'condition' => 'myExpeditionIsBehind'],
      383 => ['description' => clienttranslate('If my Expedition is behind:'), 'condition' => 'myExpeditionIsBehind'],
      357 => [
        'description' => clienttranslate('If there are no Characters in the Expedition I\'m played in:'),
        'condition' => 'XCharacterInExpedition:0:E',
      ],
      356 => [
        'description' => clienttranslate('If there are two or more exhausted cards in Reserve:'),
        'condition' => 'hasXExhaustedReserve:2',
      ],
      384 => [
        'description' => clienttranslate('If you control a <FLEETING> Character:'),
        'condition' => 'hasControl::1::fleeting',
      ],
      403 => [
        'description' => clienttranslate('If you control two or more Permanents:'),
        'condition' => 'hasControl:permanent:2',
      ],
      358 => ['description' => clienttranslate('If you have ten or more Mana Orbs:'), 'condition' => 'hasXMana:10'],
      359 => [
        'description' => clienttranslate('If you have two or more cards in Reserve:'),
        'condition' => 'checkReserveCards:2',
      ],
      361 => [
        'description' => clienttranslate('If your Companion Expedition is behind:'),
        'condition' => 'companionExpeditionIsBehind',
      ],
      362 => ['description' => clienttranslate('If your hand is empty:'), 'condition' => 'isHandEmpty'],
      364 => ['description' => clienttranslate('If your Hero Expedition is behind:'), 'condition' => 'heroExpeditionIsBehind'],
      367 => ['description' => clienttranslate('If your Reserve is empty:'), 'condition' => 'isReserveEmpty'],
      430 => [
        'description' => clienttranslate('Roll a die. On a 4+:'),
        'effect' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '4+' => 'OUTPUT',
            '1-3' => 'OPPOSITE',
          ],
        ]),
      ],
      368 => [
        'description' => clienttranslate('Sacrifice a Plant or Permanent. If you can\'t:'),
        'effect' => FT::XOR(
          FT::XOR(
            FT::ACTION(TARGET, [
              'targetPlayer' => ME,
              'targetType' => [CHARACTER],
              'subType' => PLANT,
              'excludeSelf' => true,
              'n' => 1,
              'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            ]),
            FT::ACTION(TARGET, [
              'targetPlayer' => ME,
              'targetType' => [PERMANENT],
              'excludeSelf' => true,
              'n' => 1,
              'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            ])
          ),
          FT::ACTION(CHECK_CONDITION, ['condition' => 'noPlantnoPermanent', 'effect' => 'OUTPUT'])
        ),
      ],
      369 => [
        'description' => clienttranslate('Sacrifice another Robot or Permanent. If you can\'t:'),
        'effect' => FT::XOR(
          FT::XOR(
            FT::ACTION(TARGET, [
              'targetPlayer' => ME,
              'targetType' => [CHARACTER, TOKEN],
              'subType' => ROBOT,
              'excludeSelf' => true,
              'n' => 1,
              'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            ]),
            FT::ACTION(TARGET, [
              'targetPlayer' => ME,
              'targetType' => [PERMANENT],
              'excludeSelf' => true,
              'n' => 1,
              'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            ])
          ),
          FT::ACTION(CHECK_CONDITION, ['condition' => 'noRobotnoPermanent', 'effect' => 'OUTPUT'])
        ),
      ],
      371 => ['description' => clienttranslate('Unless I\'m in {O}:'), 'condition' => 'isNotInBiome:ocean'],
      425 => [
        'description' => clienttranslate('Unless you control two or more Landmarks:'),
        'condition' => 'hasControl:landmark:1:false:all:LTE',
      ],
      373 => [
        'description' => clienttranslate('You may discard a Character from your Reserve. If you do:'),
        'effect' => FT::ACTION(
          TARGET,
          [
            'targetType' => [CHARACTER],
            'targetPlayer' => ME,
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::SEQ(FT::ACTION(DISCARD, []), 'OUTPUT'),
          ],
          ['optional' => true]
        ),
        'ifYouDo' => true,
      ],
      374 => [
        'description' => clienttranslate('You may discard a Spell from your Reserve. If you do:'),
        'effect' => FT::ACTION(
          TARGET,
          [
            'targetType' => [SPELL],
            'targetPlayer' => ME,
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::SEQ(FT::ACTION(DISCARD, []), 'OUTPUT'),
          ],
          ['optional' => true]
        ),
        'ifYouDo' => true,
      ],
      375 => [
        'description' => clienttranslate('You may have me gain <FLEETING>. If you do:'),
        'effect' => FT::SEQ_OPTIONAL(FT::GAIN(ME, FLEETING), 'OUTPUT'),
        'ifYouDo' => true,
      ],
      385 => [
        'description' => clienttranslate('You may have me gain <FLEETING>. If you do:'),
        'effect' => FT::SEQ_OPTIONAL(FT::GAIN(ME, FLEETING), 'OUTPUT'),
        'ifYouDo' => true,
      ],
      376 => [
        'description' => clienttranslate('You may ready an exhausted card in Reserve. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetLocation' => [RESERVE],
          'isTapped' => true,
          'upTo' => true,
          'effect' => FT::SEQ(FT::ACTION(READY, []), 'OUTPUT'),
        ]),
        'ifYouDo' => true,
      ],
      370 => [
        'description' => clienttranslate('You may sacrifice another Robot or Permanent. If you do:'),
        'effect' => FT::XOR_OPTIONAL(
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER, TOKEN],
            'subType' => ROBOT,
            'excludeSelf' => true,
            'n' => 1,
            'effect' => FT::SEQ(FT::ACTION(DISCARD, ['desc' => 'sacrifice']), 'OUTPUT'),
          ]),
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [PERMANENT],
            'excludeSelf' => true,
            'n' => 1,
            'effect' => FT::SEQ(FT::ACTION(DISCARD, ['desc' => 'sacrifice']), 'OUTPUT'),
          ])
        ),
        'ifYouDo' => true,
      ],
      // Bise
      521 => ['description' => clienttranslate('4+:'), 'condition' => '4+'], // TODO: attente clarification Jacques
      522 => ['description' => clienttranslate('9+:'), 'condition' => '9+'], // TODO: attente clarification Jacques
      515 => ['description' => clienttranslate('If I have 1 or more boosts:'), 'condition' => 'hasBoost'],
      516 => ['description' => clienttranslate('If I have 2 or more boosts:'), 'condition' => 'hasBoost:2'],
      509 => ['description' => clienttranslate('If I\'m <FLEETING>:'), 'condition' => 'hasFleeting'],
      510 => [
        'description' => clienttranslate('If you have less cards in Reserve than target opponent:'),
        'condition' => 'hasLessReserveCards',
      ],
      520 => [
        'description' => clienttranslate(
          'Remove all boosts from Characters in play and in Reserve. Then, depending on the number of boosts removed this way: • 1+:'
        ),
        'effect' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'manInTheMazeUnique',
          'args' => ['1+' => 'OUTPUT', '4+' => 'OUTPUT4', '9+' => 'OUTPUT9'],
        ]),
      ],
      511 => [
        'description' => clienttranslate('You may have target opponent draw a card. If you do:'),
        'effect' => FT::SEQ_OPTIONAL_MANUAL(FT::ACTION(DRAW, ['players' => OPPONENT]), 'OUTPUT'),
        'ifYouDo' => true,
      ],
      512 => [
        'description' => clienttranslate('You may spend 1 of my boosts. If you do:'),
        'effect' => FT::SEQ_OPTIONAL(FT::ACTION(SPEND, ['cardId' => ME, 'effect' => 'OUTPUT'])),
        'ifYouDo' => true,
      ],
      513 => [
        'description' => clienttranslate('You may spend 2 of my boosts. If you do:'),
        'effect' => FT::SEQ_OPTIONAL(FT::ACTION(SPEND, ['cardId' => ME, 'n' => 2, 'effect' => 'OUTPUT'])),
        'ifYouDo' => true,
      ],
      514 => [
        'description' => clienttranslate('You may spend 1 counter from a card you control or in your Reserve. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetLocation' => CONTROLLED_RESERVE,
          'targetPlayer' => ME,
          'augmentOnly' => true,
          'targetType' => TYPES,
          'upTo' => true,
          'effect' => FT::ACTION(SPEND, [
            'effect' => 'OUTPUT',
          ]),
        ]),
        'ifYouDo' => true,
      ],
      538 => ['description' => clienttranslate('If your hand is empty:'), 'condition' => 'isHandEmpty'],
      // Cyclone
      633 => [
        'description' => clienttranslate('If an opponent controls three Characters or more:'),
        'condition' => 'hasOpponentControl:character:3',
      ],
      610 => [
        'description' => clienttranslate('If both of your Expeditions are Ascended:'),
        'condition' => 'hasSourcePlayerAllAscended',
      ],
      611 => [
        'description' => clienttranslate('If five or more single-terrain regions are visible:'),
        'condition' => 'countMonoVisibleRegions:5',
      ],
      612 => ['description' => clienttranslate('If I have less than 2 boosts:'), 'condition' => 'hasBoost:1:LTE'],
      613 => ['description' => clienttranslate('If I have less than 3 boosts:'), 'condition' => 'hasBoost:2:LTE'],
      614 => ['description' => clienttranslate('If I have no boosts:'), 'condition' => 'hasBoost:0:LTE'],
      625 => ['description' => clienttranslate('If I\'m facing a Character:'), 'condition' => 'isOpponentExpeditionNotEmpty'],
      626 => ['description' => clienttranslate('If I\'m facing a Character:'), 'condition' => 'isOpponentExpeditionNotEmpty'],
      620 => [
        'description' => clienttranslate('If I\'m facing an Expedition in {M}:'),
        'condition' => 'isOpponentExpeditionIn:mountain',
      ],
      621 => [
        'description' => clienttranslate('If I\'m facing an Expedition in {O}:'),
        'condition' => 'isOpponentExpeditionIn:ocean',
      ],
      622 => [
        'description' => clienttranslate('If I\'m facing an Expedition in {V}:'),
        'condition' => 'isOpponentExpeditionIn:forest',
      ],
      628 => [
        'description' => clienttranslate('If I\'m facing only one Character:'),
        'condition' => 'isOpponentExpeditionFilled:character:1',
      ],
      648 => ['description' => clienttranslate('If I\'m in a single-terrain region:'), 'condition' => 'cardInMonoRegion'],
      615 => ['description' => clienttranslate('If I\'m in an Ascended Expedition:'), 'condition' => 'isCardExpeditionAscended'],
      616 => ['description' => clienttranslate('If I\'m in an Ascended Expedition:'), 'condition' => 'isCardExpeditionAscended'],
      617 => [
        'description' => clienttranslate('If neither of your Expeditions is in {M}:'),
        'condition' => 'allExpeditionsNotIn:mountain',
      ],
      618 => [
        'description' => clienttranslate('If one or more of your Expeditions is Ascended:'),
        'condition' => 'hasSourcePlayerAscended',
      ],
      619 => [
        'description' => clienttranslate('If one or more of your Expeditions is Ascended:'),
        'condition' => 'hasSourcePlayerAscended',
      ],
      623 => [
        'description' => clienttranslate('If there are 7 or more boosts among Characters you control and in your Reserve:'),
        'condition' => 'countOwnerBoosts:7',
      ],
      624 => [
        'description' => clienttranslate(
          'If there are nine or more base statistics of 0 among Characters you control and in your Reserve:'
        ),
        'condition' => 'hasXWithZeroStat:all:9',
      ],
      660 => ['description' => clienttranslate('If there\'s a Spell in your Reserve:'), 'condition' => 'hasReserve:spell'],
      627 => [
        'description' => clienttranslate('If there\'s a Spell with Reserve Cost {4} or more in your Reserve:'),
        'condition' => 'hasReserve:spell::4',
      ],
      661 => ['description' => clienttranslate('If there\'s an Animal in your Reserve:'), 'condition' => 'hasReserve:animal'],
      629 => [
        'description' => clienttranslate('If three or more single-terrain regions are visible:'),
        'condition' => 'countMonoVisibleRegions:3',
      ],
      630 => [
        'description' => clienttranslate('If three or more single-terrain regions are visible:'),
        'condition' => 'countMonoVisibleRegions:3',
      ],
      631 => ['description' => clienttranslate('If you are first player:'), 'condition' => 'isFirstPlayer'],
      632 => ['description' => clienttranslate('If you are first player:'), 'condition' => 'isFirstPlayer'],
      665 => ['description' => clienttranslate('If you are not first player:'), 'condition' => 'isNotFirstPlayer'],
      664 => [
        'description' => clienttranslate('If you control a Character in each of your Expeditions:'),
        'condition' => 'controlInAllExpeditions:character:true',
      ],
      662 => [
        'description' => clienttranslate('If you have less cards in hand than target opponent:'),
        'condition' => 'hasSmallerHand',
      ],
      663 => ['description' => clienttranslate('If you have six or more Mana Orbs:'), 'condition' => 'hasXMana:6'],
      634 => [
        'description' => clienttranslate('Unless I\'m facing two or more Characters:'),
        'condition' => 'isOpponentExpeditionFilled:character:1:LTE',
      ],
      635 => [
        'description' => clienttranslate('You may <RUSH>. If you do:'),
        'effect' => FT::SEQ_OPTIONAL_MANUAL(FT::RUSH(), 'OUTPUT'),
        'ifYouDo' => true,
      ],
      636 => [
        'description' => clienttranslate('You may <RUSH>. If you do:'),
        'effect' => FT::SEQ_OPTIONAL_MANUAL(FT::RUSH(), 'OUTPUT'),
        'ifYouDo' => true,
      ],
      637 => [
        'description' => clienttranslate('You may have target Character facing me gain <ANCHORED>. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetLocation' => ['opponentSource'],
          'upTo' => true,
          'effect' => FT::SEQ(FT::GAIN(EFFECT, ANCHORED), 'OUTPUT'),
        ]),
        'ifYouDo' => true,
      ],
      638 => [
        'description' => clienttranslate('You may immediately pass. If you do:'),
        'effect' => FT::SEQ_OPTIONAL_MANUAL(FT::ACTION(END_AFTERNOON, []), 'OUTPUT'),
        'ifYouDo' => true,
      ],
      639 => [
        'description' => clienttranslate('You may immediately pass. If you do:'),
        'effect' => FT::SEQ_OPTIONAL_MANUAL(FT::ACTION(END_AFTERNOON, []), 'OUTPUT'),
        'ifYouDo' => true,
      ],
      640 => [
        'description' => clienttranslate('You may put a Permanent from your hand in Reserve. If you don\'t:'),
        'effect' => FT::XOR(
          FT::ACTION(CHECK_CONDITION, [
            'condition' => 'hasControl:permanent:1',
            'description' => clienttranslate('if control permanent'),
            'effect' =>
            FT::XOR(
              FT::ACTION(
                TARGET,
                [
                  'targetLocation' => [HAND],
                  'targetPlayer' => ME,
                  'targetType' => [PERMANENT],
                  'effect' => FT::DISCARD_TO_RESERVE(),
                ]
              ),
              'OUTPUT'
            )
          ]),
          FT::ACTION(CHECK_CONDITION, [
            'condition' => 'hasControl:permanent:0:false:all:LTE',
            'description' => clienttranslate('if no permanent'),
            'effect' => FT::ACTION(
              TARGET,
              [
                'targetLocation' => [HAND],
                'targetPlayer' => ME,
                'upTo' => true,
                'targetType' => [PERMANENT],
                'effect' => FT::DISCARD_TO_RESERVE(),
              ]
            ),
          ]),
        )
      ],
      641 => [
        'description' => clienttranslate('You may put a Spell from your hand in Reserve. If you don\'t:'),
        'effect' => FT::XOR(
          FT::ACTION(TARGET, [
            'targetLocation' => [HAND],
            'targetPlayer' => ME,
            'targetType' => [SPELL],
            'effect' => FT::DISCARD_TO_RESERVE(),
          ]),
          'OUTPUT'
        ),
      ],
      642 => [
        'description' => clienttranslate('You may reveal a Character with Hand Cost {4} or more from your hand. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'upTo' => true,
          'targetType' => [CHARACTER],
          'minHandCost' => 4,
          'effect' => FT::SEQ(FT::ACTION(SPECIAL_EFFECT, ['effect' => 'reveal']), 'OUTPUT'),
        ]),
        'ifYouDo' => true,
      ],
      643 => [
        'description' => clienttranslate('You may reveal a Spell with Hand Cost {4} or more from your hand. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'upTo' => true,
          'targetType' => [SPELL],
          'minHandCost' => 4,
          'effect' => FT::SEQ(FT::ACTION(SPECIAL_EFFECT, ['effect' => 'reveal']), 'OUTPUT'),
        ]),
        'ifYouDo' => true,
      ],
      644 => [
        'description' => clienttranslate('You may sacrifice a Character or Permanent. If you don\'t:'),
        'effect' => FT::XOR(
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [PERMANENT, CHARACTER],
            'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
          ]),
          'OUTPUT'
        ),
      ],
      645 => [
        'description' => clienttranslate('You may sacrifice a Permanent. If you don\'t:'),
        'effect' => FT::XOR(
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [PERMANENT],
            'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
          ]),
          'OUTPUT'
        ),
      ],
      // Duster
      775 => [
        'description' => clienttranslate('You may pass. If you don\'t:'),
        'effect' => FT::XOR(
          FT::ACTION(END_AFTERNOON, []),
          'OUTPUT'
        )
      ],
      758 => ['description' => clienttranslate('If I\'m <ANCHORED>:'), 'condition' => 'isAnchored'],
      759 => ['description' => clienttranslate('If I\'m <IN_CONTACT>:'), 'condition' => 'isInContact'],
      760 => ['description' => clienttranslate('If I\'m <IN_CONTACT>:'), 'condition' => 'isInContact'],
      764 => ['description' => clienttranslate('If there\'s an exhausted card in your Reserve or you control an exhausted Permanent:'), 'condition' => 'hasControlExhaustedPermanentOrReserve'],
      766 => [
        'description' => clienttranslate('You may <RUSH>. If you don\'t:'),
        'effect' => FT::XOR(
          FT::RUSH(),
          'OUTPUT'
        )
      ],
      767 => [
        'description' => clienttranslate('You may <RUSH>. Unless you played a Character this way:'),
        'effect' => FT::SEQ_OPTIONAL_MANUAL(
          FT::RUSH(),
          FT::ACTION(CHECK_CONDITION, ['conditions' => ['isCardPlayedNotCharacter'], 'effect' => 'OUTPUT'])
        )
      ],
      769 => [
        'description' => clienttranslate('You may create a <MANASEED> token in target opponent\'s Landmarks. If you do:'),
        'effect' =>  FT::SEQ_OPTIONAL(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'targetPlayer' => OPPONENT,
            'tokenType' => 'NE_Common_Manaseed',
            'targetLocation' => [LANDMARK],
          ]),
          'OUTPUT'
        ),
        'ifYouDo' => true,
      ],
      761 => ['description' => clienttranslate('If I\'m <IN_CONTACT>:'), 'condition' => 'isInContact'],
      770 => [
        'description' => clienttranslate('You may exhaust a card in your Reserve. If you do:'),
        'effect' =>
        FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'targetPlayer' => ME,
          'upTo' => true,
          'isNotTapped' => true,
          'targetType' => [PERMANENT, SPELL, CHARACTER],
          'effect' => FT::SEQ(
            FT::ACTION(EXHAUST, []),
            'OUTPUT'
          )
        ]),
        'ifYouDo' => true,
      ],
      773 => [
        'description' => clienttranslate('You may exhaust a Permanent you control. If you do:'),
        'effect' => FT::ACTION(
          TARGET,
          [
            'targetType' => [PERMANENT],
            'targetPlayer' => ME,
            'upTo' => true,
            'isNotTapped' => true,
            'effect' => FT::SEQ(
              FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
              'OUTPUT'
            )
          ]
        ),
        'ifYouDo' => true,
      ],
      772 => [
        'description' => clienttranslate('You may exhaust a Permanent you control or a card in your Reserve. If you do:'),
        'effect' => FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'targetPlayer' => ME,
            'upTo' => true,
            'isNotTapped' => true,
            'effect' => FT::SEQ(
              FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
              'OUTPUT'
            )
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [SPELL, CHARACTER, PERMANENT],
            'targetPlayer' => ME,
            'isNotTapped' => true,
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::SEQ(
              FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
              'OUTPUT'
            )
          ])
        ),
        'ifYouDo' => true,
      ],
      774 => [
        'description' => clienttranslate('You may have target opponent <RESUPPLY_INF>. If you do:'),
        'effect' => FT::SEQ_OPTIONAL(
          FT::ACTION(RESUPPLY, ['player' => 'nextPlayer']),
          'OUTPUT'
        ),
        'ifYouDo' => true,
      ],
      768 => [
        'description' => clienttranslate('You may create a <MANA_MOTH> Illusion token in an opponent\'s Expedition. If you do:'),
        'effect' => FT::SEQ_OPTIONAL(
          FT::ACTION(TARGET_EXPEDITION, [
            'players' => OPPONENT,
            'effect' =>
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'YZ_Common_ManaMoth',
            ]),
          ]),
          'OUTPUT'
        ),
        'ifYouDo' => true,
      ],
      763 => ['description' => clienttranslate('If there\'s an exhausted card in Reserve:'), 'condition' => 'hasXExhaustedReserve:1'],
      765 => ['description' => clienttranslate('Unless I\'m <IN_CONTACT>:'), 'condition' => 'isNotInContact'],
      757 => [
        'description' => clienttranslate('<SABOTAGE>. If you discarded a Character this way:'),
        'effect' =>  FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [SPELL, PERMANENT],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::ACTION(DISCARD, []),
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [CHARACTER],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::SEQ(FT::ACTION(DISCARD, []), 'OUTPUT')
          ]),
        )
        // hard code the discarded check
      ],
      762 => ['description' => clienttranslate('If I\'m not <ANCHORED>:'), 'condition' => 'isNotAnchored'],
      771 => [
        'description' => clienttranslate('You may exhaust a Character in your Reserve. If you do:'),
        'effect' =>  FT::ACTION(TARGET, [
          'upTo' => true,
          'targetType' => [CHARACTER],
          'targetPlayer' => ME,
          'targetLocation' => [RESERVE],
          'isNotTapped' => true,
          'effect' => FT::SEQ(
            FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
            'OUTPUT'
          )
        ]),
        'ifYouDo' => true,
      ],
      776 => [
        'description' => clienttranslate('You may put a card from your hand in Reserve. If it\'s a Character:'),
        'effect' => FT::XOR(
          FT::ACTION(
            TARGET,
            [
              'targetType' => [SPELL, PERMANENT],
              'targetPlayer' => ME,
              'upTo' => true,
              'targetLocation' => [HAND],
              'effect' => FT::DISCARD_TO_RESERVE(),
            ],
          ),
          FT::ACTION(
            TARGET,
            [
              'targetType' => [CHARACTER],
              'targetPlayer' => ME,
              'upTo' => true,
              'targetLocation' => [HAND],
              'effect' => FT::SEQ(
                FT::DISCARD_TO_RESERVE(),
                'OUTPUT'
              )
            ],
          ),
        )
      ],
// EOLE conditions
      882 => [
        'description' => clienttranslate('Do this once for each card with my name in your discard pile:'),
        'condition' => 'hasCardInDiscardPileSourceName',
        'effect' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'doEachCardInDiscardPileSourceName',
          'args' => ['effect' => 'OUTPUT'],
        ]),
      ],
      883 => [
        'description' => clienttranslate('If my Expedition is ahead:'),
        'condition' => 'myExpeditionIsAhead',
      ],
      884 => [
        'description' => clienttranslate('If there are six or more cards in your discard pile:'),
        'condition' => 'hasDiscardPileCards:6',
      ],
      885 => [
        'description' => clienttranslate('If a card other than me in your Expeditions or your Reserve has a {D} ability:'),
        'condition' => 'hasOtherSupportCardInReserveOrExpeditions',
      ],
      887 => [
        'description' => clienttranslate('If there\'s an Animal other than me in your Expeditions or your Reserve:'),
        'condition' => 'hasOtherAnimalInReserveOrExpeditions',
      ],
      888 => [
        'description' => clienttranslate('If you control a <COMPLETED_LOW> Feat:'),
        'condition' => 'hasCompletedFeat',
      ],
      889 => [
        'description' => clienttranslate('If you control a Feat:'),
        'condition' => 'hasControlFeat:1',
      ],
      890 => [
        'description' => clienttranslate('If a {D} ability was activated this turn:'),
        'condition' => 'checkAbilityActivatedThisTurn:discard',
      ],
      891 => [
        'description' => clienttranslate('If a {T} ability was activated this turn:'),
        'condition' => 'checkAbilityActivatedThisTurn:tap',
      ],
      892 => [
        'description' => clienttranslate('If there\'s at most one card in your hand:'),
        'condition' => 'hasXCardsInHand:1:LTE',
      ],
      893 => [
        'description' => clienttranslate('If no {D} abilities were activated this turn:'),
        'condition' => 'checkAbilityActivatedThisTurnTypeCount:discard:0:LTE',
      ],
      894 => [
        'description' => clienttranslate('Unless an opponent controls an Animal:'),
        'condition' => 'hasOpponentControl:animal:0:false:all:LTE',
      ],
      895 => [
        'description' => clienttranslate('Unless there\'s a Character other than me in each of your Expeditions:'),
        'condition' => 'hasOtherCharacterInAllExpeditions:LTE',
      ],
      896 => [
        'description' => clienttranslate('Unless I\'m in {V}:'),
        'condition' => 'isNotInBiome:forest',
      ],
      897 => [
        'description' => clienttranslate('Unless I\'m in an <ASCENDED_S> Expedition:'),
        'condition' => 'isNotCardExpeditionAscended',
      ],
      898 => [
        'description' => clienttranslate('If none of your Expeditions are <ASCENDED_S>:'),
        'condition' => 'hasNoSourcePlayerAscended',
      ],
      899 => [
        'description' => clienttranslate('If there are two or more cards in your hand:'),
        'condition' => 'hasXCardsInHand:2:GTE',
      ],
      901 => [
        'description' => clienttranslate('If you control no Permanents:'),
        'condition' => 'hasControl:permanent:0:false:all:LTE',
      ],
      902 => [
        'description' => clienttranslate('If you control no Feats:'),
        'condition' => 'hasControlFeat:0:false:all:LTE',
      ],
      903 => [
        'description' => clienttranslate('Unless you discarded a card from your hand or your Reserve this turn:'),
        'condition' => 'checkAbilityNotActivatedThisTurn:discardFromHandOrReserve',
      ],
      904 => [
        'description' => clienttranslate('Unless you discarded a card from your hand this turn:'),
        'condition' => 'checkAbilityNotActivatedThisTurn:discardFromHand',
      ],
      905 => [
        'description' => clienttranslate('If there are at most five cards in your discard pile:'),
        'condition' => 'hasDiscardPileCards:5:LTE',
      ],
      906 => [
        'description' => clienttranslate('If there are one or more cards in your hand:'),
        'condition' => 'hasCardsInHand',
      ],
      907 => [
        'description' => clienttranslate('You may discard a card from your hand or sacrifice a Character. If you do:'),
        'effect' => FT::XOR_OPTIONAL(
          FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'effect' => FT::SEQ(FT::ACTION(DISCARD, []), 'OUTPUT'),
          ]),
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER, TOKEN],
            'effect' => FT::SEQ(FT::ACTION(DISCARD, ['desc' => 'sacrifice']), 'OUTPUT'),
          ])
        ),
        'ifYouDo' => true,
      ],
      908 => [
        'description' => clienttranslate('You may discard a card from your hand. If you do:'),
        'effect' => FT::ACTION(
          TARGET,
          [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'upTo' => true,
            'effect' => FT::SEQ(FT::ACTION(DISCARD, []), 'OUTPUT'),
          ],
          ['optional' => true]
        ),
        'ifYouDo' => true,
      ],
      910 => [
        'description' => clienttranslate('You may spend 1 boost from a Character you control. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetLocation' => CONTROLLED_RESERVE,
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'augmentOnly' => true,
          'upTo' => true,
          'effect' => FT::ACTION(SPEND, ['effect' => 'OUTPUT']),
        ]),
        'ifYouDo' => true,
      ],
      911 => [
        'description' => clienttranslate('You may target a Character other than me with {V} less than or equal to mine. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => STORMS,
          'excludeSelf' => true,
          'upTo' => true,
          'compareTargetBiome' => ['biome' => FOREST, 'op' => 'lte', 'source' => 'source'],
          'effect' => FT::ACTION(CHECK_CONDITION, [
            'condition' => 'isTargetForestLessOrEqualToSource',
            'effect' => 'OUTPUT',
            'oppositeEffect' => 'OPPOSITE',
          ]),
        ]),
        'ifYouDo' => true,
      ],
      912 => [
        'description' => clienttranslate('You may target a Character other than me with {V} less than or equal to mine. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => STORMS,
          'excludeSelf' => true,
          'upTo' => true,
          'compareTargetBiome' => ['biome' => FOREST, 'op' => 'lte', 'source' => 'source'],
          'effect' => FT::ACTION(CHECK_CONDITION, [
            'condition' => 'isTargetForestLessOrEqualToSource',
            'effect' => 'OUTPUT',
            'oppositeEffect' => 'OPPOSITE',
          ]),
        ]),
        'ifYouDo' => true,
      ],
      913 => [
        'description' => clienttranslate('You may target a Character other than me with {V} less than or equal to mine. If you do:'),
        'effect' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => STORMS,
          'excludeSelf' => true,
          'upTo' => true,
          'compareTargetBiome' => ['biome' => FOREST, 'op' => 'lte', 'source' => 'source'],
          'effect' => FT::ACTION(CHECK_CONDITION, [
            'condition' => 'isTargetForestLessOrEqualToSource',
            'effect' => 'OUTPUT',
            'oppositeEffect' => 'OPPOSITE',
          ]),
        ]),
        'ifYouDo' => true,
      ],
      941 => [
        'description' => clienttranslate('If there\'s a card with no {D} ability in your Reserve:'),
        'condition' => 'hasAtLeastOneCardWithNoSupportInReserve',
      ],
      942 => [
        'description' => clienttranslate('Roll a die. On a 1-3:'),
        'effect' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1-3' => 'OUTPUT',
            '4+' => 'OPPOSITE',
          ],
        ]),
      ],
      943 => [
        'description' => clienttranslate('Unless you control a [BOOSTED] Character other than me:'),
        'condition' => 'hasControl:character:0:true:boosted:LTE',
      ],  
    ];
  }

  public static function getOutput()
  {
    return [
      29 => [
        'description' => clienttranslate('Sacrifice two Characters.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'n' => 2,
          'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
        ]),
      ],
      30 => [
        'description' => clienttranslate('Target opponent draws a card.'),
        'output' => FT::ACTION(DRAW, ['players' => OPPONENT]),
      ],
      31 => [
        'description' => clienttranslate('I can\'t be played if you have less than seven Mana Orbs.'),
        'attributes' => ['minManaOrbs' => 7],
      ],
      32 => [
        'description' => clienttranslate('I am <DEFENDER>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicDefender' => 'fullDefender'],
      ],
      33 => ['description' => clienttranslate('I gain <ASLEEP>.'), 'output' => FT::GAIN(ME, ASLEEP)],
      34 => ['description' => clienttranslate('I gain <FLEETING>.'), 'output' => FT::GAIN(ME, FLEETING)],
      35 => [
        'description' => clienttranslate('I can\'t be played if you have less than six Mana Orbs.'),
        'attributes' => ['minManaOrbs' => 6],
      ],
      36 => [
        'description' => clienttranslate('Sacrifice a Character in my Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => ['source'],
          'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
        ]),
      ],
      37 => ['description' => clienttranslate('I lose $<FLEETING>.'), 'output' => FT::LOOSE(ME, FLEETING)],
      38 => [
        'description' => clienttranslate('You may play me for free and I gain <ASLEEP>.'),
        'output' => FT::SEQ_OPTIONAL(FT::ACTION(PLAY_CARD, ['cardId' => ME, 'free' => true]), FT::GAIN(ME, ASLEEP)),
      ],
      39 => ['description' => clienttranslate('Each player draws a card.'), 'output' => FT::ACTION(DRAW, [])],
      40 => [
        'description' => clienttranslate(
          'Each player puts the top card of their deck in their Mana zone (as an exhausted Mana Orb).'
        ),
        'output' => FT::ACTION(DRAW, ['location' => MANA, 'tapped' => true]),
      ],
      41 => [
        'description' => clienttranslate('You may put a card from your hand in Reserve.'),
        'output' => FT::ACTION(
          TARGET,
          [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'upTo' => true,
            'targetLocation' => [HAND],
            'effect' => FT::DISCARD_TO_RESERVE(),
          ],
          ['optional' => true]
        ),
      ],
      42 => [
        'description' => clienttranslate('You may have target Character other than me lose <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'excludeSelf' => true,
          'targetLocation' => STORMS,
          'upTo' => true,
          'effect' => FT::LOOSE(EFFECT, FLEETING),
        ]),
      ],
      43 => [
        'description' => clienttranslate('Each player may <RESUPPLY_INF>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerOptionalResupply']),
      ],
      44 => [
        'description' => clienttranslate('Put me in Reserve.'),
        'output' => FT::ACTION(DISCARD, ['destination' => RESERVE, 'cardId' => ME]),
      ],
      45 => [
        'description' => clienttranslate('The {j}, {h} and {r} abilities of Characters facing me can\'t activate.'),
        'noTrigger' => true,
        'attributes' => ['dynamicBlockingPower' => 'block'],
      ],
      46 => ['description' => clienttranslate('I have <SEASONED>.'), 'attributes' => ['seasoned' => true]],
      47 => [
        'description' => clienttranslate('I have <TOUGH_1>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicTough' => 'tough1'],
      ],
      48 => [
        'description' => clienttranslate(
          'If you would roll one or more dice, instead roll that many dice plus one and ignore the roll of your choice.'
        ),
        'attributes' => ['addDice' => 1],
      ],
      49 => [
        'description' => clienttranslate(
          'If you would roll a die, you may add 1 to its result. (Choose after you see the result.)'
        ),
        'attributes' => ['addRoll' => 1],
      ],
      50 => [
        'description' => clienttranslate('I have <TOUGH_X>, where X is the number of regions between your Hero and Companion.'),
        'noTrigger' => true,
        'attributes' => ['dynamicTough' => 'region'],
      ],
      51 => ['description' => clienttranslate('<RESUPPLY>.'), 'output' => FT::ACTION(RESUPPLY, [])],
      52 => [
        'description' => clienttranslate('You may return a Spell from your Reserve to your hand.'),
        'output' => FT::ACTION(
          TARGET,
          [
            'targetLocation' => [RESERVE],
            'targetPlayer' => ME,
            'targetType' => [SPELL],
            'effect' => FT::RETURN_TO_HAND(),
          ],
          ['optional' => true]
        ),
      ],
      54 => [
        'description' => clienttranslate('I have <TOUGH_2>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicTough' => 'tough2'],
      ],
      56 => ['description' => clienttranslate('I gain 1 boost.'), 'output' => FT::GAIN(ME, BOOST)],
      57 => [
        'description' => clienttranslate('The next Permanent you play this Afternoon costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 1, 'permanent' => true]],
        ],
      ],
      58 => [
        'description' => clienttranslate('The next Plant you play this Afternoon costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PLANT, 'reduction' => 1, 'permanent' => true]],
        ],
      ],
      59 => [
        'description' => clienttranslate('The next Spell you play this Afternoon costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => SPELL, 'reduction' => 1, 'permanent' => true]],
        ],
      ],
      60 => [
        'description' => clienttranslate(
          'The next Character you play from your hand this turn activates its {r} abilities (as if it had been played from Reserve).'
        ),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'triggerEffectOfNextCharacter',
          'args' => ['type' => CHARACTER, 'from' => HAND, 'effect' => RESERVE],
        ]),
      ],
      61 => [
        'description' => clienttranslate('You may return a card other than me from your Reserve to your hand.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetPlayer' => ME,
          'excludeSelf' => true,
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::RETURN_TO_HAND(),
        ]),
      ],
      63 => [
        'description' => clienttranslate('The next Bureaucrat you play this Afternoon costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => BUREAUCRAT, 'reduction' => 1, 'permanent' => true]],
        ],
      ],
      64 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in my Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => ['source'],
        ]),
      ],
      65 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in your Companion Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => [STORM_RIGHT],
        ]),
      ],
      66 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in your Hero Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => [STORM_LEFT],
        ]),
      ],
      67 => [
        'description' => clienttranslate('Plants you control other than me gain 1 boost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'boostAllSubtype',
          'args' => ['excludeSelf' => true, 'subType' => PLANT],
        ]),
      ],
      68 => [
        'description' => clienttranslate('Roll a die. On a 4+, I gain 2 boosts. On a 1-3, I gain 1 boost.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => ['1-3' => FT::GAIN(ME, BOOST, 1), '4+' => FT::GAIN(ME, BOOST, 2)],
        ]),
      ],
      69 => [
        'description' => clienttranslate('Up to one target Character gains <ASLEEP>.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::GAIN(EFFECT, ASLEEP),
        ]),
      ],
      70 => [
        'description' => clienttranslate('Up to one target Character gains <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::GAIN(EFFECT, FLEETING),
        ]),
      ],
      71 => [
        'description' => clienttranslate('Reduce my cost by {1}.'),
        'noTrigger' => true,
        'attributes' => ['dynamicCostReduction' => '1'],
      ],
      72 => [
        'description' => clienttranslate(
          'If you would <RESUPPLY_INF>, instead look at the top two cards of your deck. Put one in Reserve, and discard the other.'
        ),
        'attributes' => ['resupply2' => true],
      ],
      73 => [
        'description' => clienttranslate('Characters you control other than me have <TOUGH_1>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicTough' => 'universalCharacter1', 'excludeUniversalTough' => true],
      ],
      75 => [
        'description' => clienttranslate(
          'Create an <ORDIS_RECRUIT> Soldier token in your other Expedition (the one I\'m not in).'
        ),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => ['oppositeSource'],
        ]),
      ],
      76 => [
        'description' => clienttranslate('Robots you control other than me gain 1 boost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'boostAllSubtype',
          'args' => ['subType' => ROBOT, 'excludeSelf' => true],
        ]),
      ],
      77 => [
        'description' => clienttranslate('Target Character gains 1 boost.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
      ],
      78 => [
        'description' => clienttranslate('Target Character switches Expedition.'),
        'output' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'effect' => FT::ACTION(MOVE_CARD, [])]),
      ],
      79 => [
        'description' => clienttranslate('The next card you play this Afternoon costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => ALL, 'permanent' => true, 'reduction' => 1]],
        ],
      ],
      80 => [
        'description' => clienttranslate('<SABOTAGE>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      81 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in target Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'OD_Common_OrdisRecruit',
        ]),
      ],
      82 => [
        'description' => clienttranslate('I gain 1 boost for each card in your Reserve.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXReserve']),
      ],
      83 => [
        'description' => clienttranslate('Roll a die. On a 4+, draw a card. On a 1-3, <RESUPPLY>.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1-3' => FT::ACTION(RESUPPLY, []),
            '4+' => FT::ACTION(DRAW, ['players' => ME]),
          ],
        ]),
      ],
      84 => [
        'description' => clienttranslate('Your Characters other than me have: \"{R} I gain 1 boost.\"'),
        'passive' => [
          'ChooseAssignment' => [
            'conditions' => ['isCharacterFromReserveNotBlocked', 'excludeSelf'],
            'output' => FT::GAIN(EFFECT, BOOST),
          ],
        ],
      ],
      86 => [
        'description' => clienttranslate('I gain 1 boost for each Landmark you control.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXLandmark']),
      ],
      87 => [
        'description' => clienttranslate('You may activate the {j} abilities of target Permanent you control.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'targetPlayer' => ME,
          'hasEffects' => ['Played'],
          'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
        ]),
      ],
      88 => [
        'description' => clienttranslate('You may have target Character other than me lose <FLEETING> and gain 1 boost.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'excludeSelf' => true,
          'effect' => FT::SEQ(FT::LOOSE(EFFECT, FLEETING), FT::GAIN(EFFECT, BOOST)),
        ]),
      ],
      89 => [
        'description' => clienttranslate('Each player sacrifices a Character.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'AllPlayersSacrifice1']),
      ],
      90 => ['description' => clienttranslate('Draw a card.'), 'output' => FT::ACTION(DRAW, ['players' => ME])],
      91 => [
        'description' => clienttranslate('Up to one target Character gains <ASLEEP>. You may have it gain 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'effect' => FT::XOR(FT::GAIN(EFFECT, ASLEEP), FT::SEQ(FT::GAIN(EFFECT, ASLEEP), FT::GAIN(EFFECT, BOOST, 2))),
        ]),
      ],
      92 => [
        'description' => clienttranslate('Each Character controlled by target player gains <FLEETING>.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'fleetingAllCharacters']),
        ]),
      ],
      94 => [
        'description' => clienttranslate('Up to one target Character with Hand Cost {3} or less other than me gains <ANCHORED>.'),
        'output' => FT::ACTION(TARGET, [
          'maxHandCost' => 3,
          'excludeSelf' => true,
          'upTo' => true,
          'effect' => FT::GAIN(EFFECT, ANCHORED),
        ]),
      ],
      95 => [
        'description' => clienttranslate('Characters your opponents play can\'t cost less than {2}.'),
        'noTrigger' => true,
        'attributes' => ['opponentCharactersMinimumCost' => '2'],
      ],
      96 => [
        'description' => clienttranslate('Put me in my owner\'s Mana zone (as an exhausted Mana Orb).'),
        'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => MANA, 'tapped' => true, 'force' => true]),
      ],
      97 => ['description' => clienttranslate('I gain 2 boosts.'), 'output' => FT::GAIN(ME, BOOST, 2)],
      98 => [
        'description' => clienttranslate(
          'You may return target Character or Permanent with Hand Cost {4} or less to its owner\'s hand.'
        ),
        'output' => FT::ACTION(TARGET, [
          'maxHandCost' => 4,
          'upTo' => true,
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'effect' => FT::RETURN_TO_HAND(),
        ]),
      ],
      99 => [
        'description' => clienttranslate('Roll a die. On a 4+, I gain 3 boosts. On a 1-3, I gain 1 boost.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => ['1-3' => FT::GAIN(ME, BOOST, 1), '4+' => FT::GAIN(ME, BOOST, 3)],
        ]),
      ],
      100 => [
        'description' => clienttranslate('Target Character gains <ANCHORED>.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::GAIN(EFFECT, ANCHORED)]),
      ],
      101 => [
        'description' => clienttranslate('Target Character in your other Expedition (the one I\'m not in) gains 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => ['oppositeSource'],
          'targetPlayer' => ME,
          'effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 2]),
        ]),
      ],
      102 => [
        'description' => clienttranslate('The next Permanent you play this Afternoon costs {2} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 2, 'permanent' => true]],
        ],
      ],
      104 => [
        'description' => clienttranslate('You may discard target Permanent with Hand Cost {4} or more.'),
        'output' => FT::ACTION(TARGET, [
          'minHandCost' => 4,
          'upTo' => true,
          'targetType' => [PERMANENT],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      105 => [
        'description' => clienttranslate('You may put me in my owner\'s Mana zone (as an exhausted Mana Orb).'),
        'output' => FT::ACTION(DISCARD, [
          'cardId' => ME,
          'destination' => MANA,
          'tapped' => true,
          'canPass' => true,
          'force' => true,
        ]),
      ],
      106 => [
        'description' => clienttranslate('I gain 2 boosts and lose <FLEETING>.'),
        'output' => FT::SEQ(FT::GAIN(ME, BOOST, 2), FT::LOOSE(ME, FLEETING)),
      ],
      107 => [
        'description' => clienttranslate('Create two <ORDIS_RECRUIT> Soldier tokens in my Expedition.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => ['initialSource'],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => ['initialSource'],
            'moreThan1' => true,
          ])
        ),
      ],
      108 => [
        'description' => clienttranslate('I gain 1 boost for each card in each player\'s Reserve.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXReserveAll']),
        'attributes' => ['blockAutomaticAction' => [GAIN => [BOOST => 1]]],
      ],
      109 => [
        'description' => clienttranslate('Up to one target Plant gains 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'subType' => PLANT,
          'effect' => FT::GAIN(EFFECT, BOOST, 2),
        ]),
      ],
      110 => ['description' => clienttranslate('I gain <ANCHORED>.'), 'output' => FT::GAIN(ME, ANCHORED)],
      111 => [
        'description' => clienttranslate('Put the top card of your deck in your Mana zone (as an exhausted Mana Orb).'),
        'output' => FT::ACTION(DRAW_MANA, []),
      ],
      112 => [
        'description' => clienttranslate('Cards your opponents play can\'t cost less than {2}.'),
        'noTrigger' => true,
        'attributes' => ['opponentCardsMinimumCost' => '2'],
      ],
      113 => [
        'description' => clienttranslate('You may activate the {j} abilities of up to two target Permanents you control.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'targetPlayer' => ME,
          'upTo' => true,
          'n' => 2,
          'hasEffects' => ['Played'],
          'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
        ]),
      ],
      114 => [
        'description' => clienttranslate('Reduce my cost by {2}.'),
        'noTrigger' => true,
        'attributes' => ['dynamicCostReduction' => '2'],
      ],
      115 => [
        'description' => clienttranslate(
          'You may return target Character or Permanent with Hand Cost {5} or less to its owner\'s hand.'
        ),
        'output' => FT::ACTION(TARGET, [
          'maxHandCost' => 5,
          'upTo' => true,
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'effect' => FT::RETURN_TO_HAND(),
        ]),
      ],
      116 => [
        'description' => clienttranslate('Target Character other than me gains <FLEETING>, <ANCHORED> or <ASLEEP>.'),
        'output' => FT::ACTION(TARGET, [
          'excludeSelf' => true,
          'effect' => FT::XOR(FT::GAIN(EFFECT, FLEETING), FT::GAIN(EFFECT, ANCHORED), FT::GAIN(EFFECT, ASLEEP)),
        ]),
      ],
      117 => [
        'description' => clienttranslate('Characters you control other than me have <TOUGH_2>.'),
        'attributes' => ['excludeUniversalTough' => true, 'dynamicTough' => 'universalCharacter2'],
        'noTrigger' => true,
      ],
      118 => [
        'description' => clienttranslate('Create a <BRASSBUG> Robot token in target Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => STORMS,
        ]),
      ],
      119 => [
        'description' => clienttranslate('Target Character gains 2 boosts.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 2])]),
      ],
      120 => [
        'description' => clienttranslate('Up to two target Characters each gain 1 boost.'),
        'output' => FT::ACTION(TARGET, ['upTo' => true, 'n' => 2, 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
      ],
      121 => [
        'description' => clienttranslate(
          'You may send to Reserve target Character with Hand Cost {X} or less, where X is the number of Characters you control.'
        ),
        'output' => FT::ACTION(TARGET, [
          'maxHandCost' => 'controlledCharacter',
          'upTo' => true,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      122 => [
        'description' => clienttranslate(
          'You may put me in my owner\'s Mana zone (as an exhausted Mana Orb). If you don\'t, draw a card.'
        ),
        'output' => FT::XOR(
          FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => MANA, 'force' => true, 'tapped' => true]),
          FT::ACTION(DRAW, ['players' => ME])
        ),
      ],
      123 => ['description' => clienttranslate('I am <GIGANTIC>.'), 'attributes' => ['gigantic' => true]],
      124 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in each of your Expeditions.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => [STORM_RIGHT],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => [STORM_LEFT],
            'moreThan1' => true,
          ])
        ),
      ],
      125 => [
        'description' => clienttranslate('Each player discards their hand, then draws three cards.'),
        'output' => FT::SEQ(FT::ACTION(SPECIAL_EFFECT, ['effect' => 'discardAllHand']), FT::ACTION(DRAW, ['n' => 3])),
      ],
      127 => [
        'description' => clienttranslate('For each Character you control other than me, you may activate its {j} triggers.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'activateAllOtherCharacters']),
      ],
      128 => [
        'description' => clienttranslate('For each Permanent you control, you may activate its {j} triggers.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'activateAllPermanents']),
      ],
      129 => [
        'description' => clienttranslate('You may send to Reserve target Character with Hand Cost {3} or less.'),
        'output' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'upTo' => true, 'effect' => FT::DISCARD_TO_RESERVE()]),
      ],
      130 => [
        'description' => clienttranslate('Target opponent discards a card from their hand.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'effect' => FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        ]),
      ],
      131 => [
        'description' => clienttranslate(
          'Your other Expedition (the one I\'m not in) and the Expedition facing it can\'t move forward.'
        ),
        'noTrigger' => true,
        'attributes' => ['dynamicOppositeDefender' => 't'],
      ], // value is not relevant :)
      132 => [
        'description' => clienttranslate('You may discard target Permanent.'),
        'output' => FT::ACTION(TARGET, ['targetType' => [PERMANENT], 'upTo' => true, 'effect' => FT::ACTION(DISCARD, [])]),
      ],
      133 => [
        'description' => clienttranslate('Characters you control gain 1 boost.'),
        'output' => FT::ACTION(TARGET, ['targetPlayer' => ME, 'n' => INFTY, 'effect' => FT::GAIN(EFFECT, BOOST)]),
      ],
      134 => ['description' => clienttranslate('I gain 3 boosts.'), 'output' => FT::GAIN(ME, BOOST, 3)],
      136 => [
        'description' => clienttranslate('You may send to Reserve target Character with Hand Cost {4} or more.'),
        'output' => FT::ACTION(TARGET, ['minHandCost' => 4, 'upTo' => true, 'effect' => FT::DISCARD_TO_RESERVE()]),
      ],
      137 => [
        'description' => clienttranslate('You may discard target Character with Hand Cost {3} or less.'),
        'output' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'upTo' => true, 'effect' => FT::ACTION(DISCARD, [])]),
      ],
      138 => [
        'description' => clienttranslate('You may discard target <FLEETING>, <ANCHORED>, or <ASLEEP> Character.'),
        'output' => FT::ACTION(TARGET, [
          'statuses' => [FLEETING, ANCHORED, ASLEEP],
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      139 => [
        'description' => clienttranslate(
          'You may put target Character or Permanent in its owner\'s Mana zone (as an exhausted Mana Orb).'
        ),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT, CHARACTER, TOKEN],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, ['destination' => MANA, 'tapped' => true]),
        ]),
      ],
      140 => [
        'description' => clienttranslate('Roll a die. I gain X boosts, where X is the result.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => ['1+' => FT::GAIN(ME, BOOST, 'die')],
        ]),
      ],
      141 => [
        'description' => clienttranslate('You may discard target Character with Hand Cost {4} or more.'),
        'output' => FT::ACTION(TARGET, ['minHandCost' => 4, 'upTo' => true, 'effect' => FT::ACTION(DISCARD, [])]),
      ],
      142 => [
        'description' => clienttranslate('I am <ETERNAL>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicEternal' => '1'],
      ],
      143 => [
        'description' => clienttranslate('Each player discards their hand and their Reserve, then draws three cards.'),
        'output' => FT::SEQ(FT::ACTION(SPECIAL_EFFECT, ['effect' => 'discardAllHandReserve']), FT::ACTION(DRAW, ['n' => 3])),
      ],
      144 => [
        'description' => clienttranslate('You may send target Character to Reserve.'),
        'output' => FT::ACTION(TARGET, ['upTo' => true, 'effect' => FT::DISCARD_TO_RESERVE()]),
      ],
      145 => [
        'description' => clienttranslate('Roll a die. Target Character gain X boosts, where X is the result.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => ['1+' => FT::ACTION(TARGET, ['effect' => FT::GAIN('effect', BOOST, 'die')])],
        ]),
      ],
      146 => [
        'description' => clienttranslate('Target Character gains 3 boosts.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 3])]),
      ],
      147 => [
        'description' => clienttranslate('You may discard target Character.'),
        'output' => FT::ACTION(TARGET, ['targetType' => [TOKEN, CHARACTER], 'upTo' => true, 'effect' => FT::ACTION(DISCARD, [])]),
      ],
      148 => [
        'description' => clienttranslate('All Characters in target Expedition gain <ASLEEP>.'),
        'output' => FT::ACTION(TARGET_EXPEDITION, [
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'sleepingAllCharactersinExpedition']),
        ]),
      ],
      149 => [
        'description' => clienttranslate('Your opponent\'s Expedition facing me moves backwards one region.'),
        'output' => FT::ACTION(MOVE_EXPEDITION, ['n' => -1, 'expedition' => [EFFECT], 'pId' => OPPONENT]),
      ],
      150 => [
        'description' => clienttranslate('You may return target Character or Permanent to the top of its owner\'s deck.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, ['destination' => 'topOfDeck']),
        ]),
      ],
      151 => [
        'description' => clienttranslate('Up to two target Characters each gain 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'n' => 2,
          'effect' => FT::GAIN(EFFECT, BOOST, 2),
        ]),
      ],
      152 => ['description' => clienttranslate('Draw two cards.'), 'output' => FT::ACTION(DRAW, ['n' => 2, 'players' => ME])],
      154 => [
        'description' => clienttranslate('You may discard target Character or Permanent.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT, CHARACTER, TOKEN],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      155 => [
        'description' => clienttranslate('Put the top two cards of your deck in your Mana zone (as exhausted Mana Orbs).'),
        'output' => FT::ACTION(DRAW_MANA, ['n' => 2]),
      ],
      156 => [
        'description' => clienttranslate('Create a <BRASSBUG> Robot token in each of your Expeditions.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'AX_Common_Brassbug',
            'targetLocation' => [STORM_RIGHT],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'AX_Common_Brassbug',
            'targetLocation' => [STORM_LEFT],
            'moreThan1' => true,
          ])
        ),
      ],
      157 => [
        'description' => clienttranslate('Create two <ORDIS_RECRUIT> Soldier tokens in each of your Expeditions.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => [STORM_RIGHT],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => [STORM_RIGHT],
            'moreThan1' => true,
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => [STORM_LEFT],
            'moreThan1' => true,
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => [STORM_LEFT],
            'moreThan1' => true,
          ])
        ),
      ],
      158 => ['description' => clienttranslate('Draw three cards.'), 'output' => FT::ACTION(DRAW, ['n' => 3, 'players' => ME])],
      159 => [
        'description' => clienttranslate(
          'Create four <ORDIS_RECRUIT> Soldier tokens, distributed as you choose among any number of target Expeditions.'
        ),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => STORMS,
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => STORMS,
            'moreThan1' => true,
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => STORMS,
            'moreThan1' => true,
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => STORMS,
            'moreThan1' => true,
          ])
        ),
      ],
      193 => ['description' => clienttranslate('<AFTER_YOU>.'), 'output' => FT::ACTION(AFTER_YOU, [])],
      194 => [
        'description' => clienttranslate('Tokens you control are <GIGANTIC>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicGigantic' => 'universalGiganticToken'],
      ],
      195 => [
        'description' => clienttranslate('Roll a die. On a 4+, I gain <ANCHORED>. On a 1-3, I gain 3 boosts.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => ['1-3' => FT::GAIN(ME, BOOST, 3), '4+' => FT::GAIN(ME, ANCHORED)],
        ]),
      ],
      196 => [
        'description' => clienttranslate('Sacrifice one Character.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
        ]),
      ],
      197 => [
        'description' => clienttranslate('All Regions are {O} and lose their other types.'),
        'attributes' => [
          'updateExpeditions' => ['type' => 'all', 'regionsRemove' => [MOUNTAIN, FOREST], 'regionsAdd' => [OCEAN]],
        ],
      ],
      199 => [
        'description' => clienttranslate('The next Spell you play this turn loses <FLEETING>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'removeFleetingSpellPlayed']),
      ],
      200 => [
        'description' => clienttranslate('The next Character you play this turn loses <FLEETING>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'removeFleetingCharacterPlayed']),
      ],
      203 => [
        'description' => clienttranslate('<SABOTAGE>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      204 => ['description' => clienttranslate('I have <SEASONED>. (OOF)'), 'attributes' => ['seasoned' => true]],
      205 => [
        'description' => clienttranslate('Up to one target Character with Hand Cost {3} or less other than me gains <ANCHORED>.'),
        'output' => FT::ACTION(TARGET, [
          'maxHandCost' => 3,
          'excludeSelf' => true,
          'upTo' => true,
          'effect' => FT::GAIN(EFFECT, ANCHORED),
        ]),
      ],
      206 => [
        'description' => clienttranslate('Roll a die. On a 4+, I gain 2 boosts. On a 1-3, I gain 1 boost.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => ['1-3' => FT::GAIN(ME, BOOST, 1), '4+' => FT::GAIN(ME, BOOST, 2)],
        ]),
      ],
      207 => [
        'description' => clienttranslate('Roll a die. On a 4+, I gain 3 boosts. On a 1-3, I gain 1 boost.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => ['1-3' => FT::GAIN(ME, BOOST, 1), '4+' => FT::GAIN(ME, BOOST, 3)],
        ]),
      ],
      208 => [
        'description' => clienttranslate('Roll a die. On a 4+, draw a card. On a 1-3, <RESUPPLY>.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1-3' => FT::ACTION(RESUPPLY, []),
            '4+' => FT::ACTION(DRAW, ['players' => ME]),
          ],
        ]),
      ],
      209 => [
        'description' => clienttranslate('Roll a die. On a 4+, I gain <ANCHORED>. On a 1-3, I gain 3 boosts.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => ['1-3' => FT::GAIN(ME, BOOST, 3), '4+' => FT::GAIN(ME, ANCHORED)],
        ]),
      ],
      210 => ['description' => clienttranslate('Each player draws a card.'), 'output' => FT::ACTION(DRAW, [])],
      211 => ['description' => clienttranslate('I gain <ANCHORED>.'), 'output' => FT::GAIN(ME, ANCHORED)],
      212 => ['description' => clienttranslate('I gain <ANCHORED>.'), 'output' => FT::GAIN(ME, ANCHORED)],
      213 => ['description' => clienttranslate('I gain <ANCHORED>.'), 'output' => FT::GAIN(ME, ANCHORED)],
      214 => [
        'description' => clienttranslate('Characters  your opponents play can\'t cost less than {2}.'),
        'noTrigger' => true,
        'attributes' => ['opponentCharactersMinimumCost' => '2'],
      ],
      215 => [
        'description' => clienttranslate('Create a <BRASSBUG> Robot token in each of your Expeditions.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'AX_Common_Brassbug',
            'targetLocation' => [STORM_RIGHT],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'AX_Common_Brassbug',
            'targetLocation' => [STORM_LEFT],
            'moreThan1' => true,
          ])
        ),
      ],
      216 => [
        'description' => clienttranslate('Create a <BRASSBUG> Robot token in target Expedition. (BR)'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => STORMS,
        ]),
      ],
      217 => [
        'description' => clienttranslate('Each Character controlled by target player gains <FLEETING>.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'fleetingAllCharacters']),
        ]),
      ],
      218 => [
        'description' => clienttranslate('Each player may <RESUPPLY_INF>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerOptionalResupply']),
      ],
      219 => [
        'description' => clienttranslate(
          'Each player puts the top card of their deck in their Mana zone (as an exhausted Mana Orb).'
        ),
        'output' => FT::ACTION(DRAW, ['location' => MANA, 'tapped' => true]),
      ],
      221 => ['description' => clienttranslate('I gain <ASLEEP>.'), 'output' => FT::GAIN(ME, ASLEEP)],
      222 => [
        'description' => clienttranslate('Sacrifice a Character in my Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => ['source'],
          'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
        ]),
      ],
      223 => [
        'description' => clienttranslate('Sacrifice one Character.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
        ]),
      ],
      224 => [
        'description' => clienttranslate('Sacrifice two Characters.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'n' => 2,
          'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
        ]),
      ],
      225 => [
        'description' => clienttranslate('Target opponent discards a card from their hand.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'effect' => FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        ]),
      ],
      226 => [
        'description' => clienttranslate('The {j}, {h} and {r} abilities of Characters facing me can\'t activate.'),
        'noTrigger' => true,
        'attributes' => ['dynamicBlockingPower' => 'block'],
      ],
      228 => [
        'description' => clienttranslate('You may put me in my owner\'s Mana zone (as an exhausted Mana Orb).'),
        'output' => FT::ACTION(DISCARD, [
          'cardId' => ME,
          'destination' => MANA,
          'tapped' => true,
          'canPass' => true,
          'force' => true,
        ]),
      ],
      229 => [
        'description' => clienttranslate(
          'You may put me in my owner\'s Mana zone (as an exhausted Mana Orb). If you don\'t, draw a card.'
        ),
        'output' => FT::XOR(
          FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => MANA, 'force' => true, 'tapped' => true]),
          FT::ACTION(DRAW, ['players' => ME])
        ),
      ],
      230 => [
        'description' => clienttranslate('Target opponent draws a card.'),
        'output' => FT::ACTION(DRAW, ['players' => OPPONENT]),
      ],
      232 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in my Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => ['source'],
        ]),
      ],
      233 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in target Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
        ]),
      ],
      234 => [
        'description' => clienttranslate('The next Character you play this turn gains 1 boost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains1Boost']),
      ],
      237 => ['description' => clienttranslate('<AFTER_YOU>.'), 'output' => FT::ACTION(AFTER_YOU, [])],
      238 => [
        'description' => clienttranslate('Target Character gains <ANCHORED>.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::GAIN(EFFECT, ANCHORED)]),
      ],
      241 => [
        'description' => clienttranslate('The next Character you play this turn gains 2 boosts.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains2Boost']),
      ],
      242 => ['description' => clienttranslate('<RESUPPLY>.'), 'output' => FT::ACTION(RESUPPLY, [])],
      243 => ['description' => clienttranslate('I gain <FLEETING>.'), 'output' => FT::GAIN(ME, FLEETING)],
      244 => [
        'description' => clienttranslate('You may activate the {j} abilities of target Permanent you control.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'upTo' => true,
          'targetPlayer' => ME,
          'hasEffects' => ['Played'],
          'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
        ]),
      ],
      245 => ['description' => clienttranslate('Draw a card.'), 'output' => FT::ACTION(DRAW, ['players' => ME])],
      246 => ['description' => clienttranslate('I am <GIGANTIC>.'), 'attributes' => ['gigantic' => true]],
      248 => [
        'description' => clienttranslate('You may return a card from your Reserve to your hand.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN, PERMANENT, SPELL],
          'upTo' => true,
          'effect' => FT::RETURN_TO_HAND(),
        ]),
      ],
      // Alizé
      301 => [
        'description' => clienttranslate('<DEFENDER> Characters don\'t prevent my Expedition from moving forward.'),
        'noTrigger' => true,
        'attributes' => ['dynamicIgnoreDefender' => '1'],
      ],
      386 => [
        'description' => clienttranslate('<DEFENDER> Characters don\'t prevent my Expedition from moving forward.'),
        'noTrigger' => true,
        'attributes' => ['dynamicIgnoreDefender' => '1'],
      ],
      264 => ['description' => clienttranslate('<EXHAUSTED_RESUPPLY>.'), 'output' => FT::ACTION(RESUPPLY, ['exhausted' => true])],
      404 => [
        'description' => clienttranslate('<RESUPPLY>, otherwise <EXHAUSTED_RESUPPLY>.'),
        'output' => FT::ACTION(RESUPPLY, []),
        'oppositeOutput' => FT::ACTION(RESUPPLY, ['exhausted' => true]),
      ],
      402 => [
        'description' => clienttranslate('<SABOTAGE>, otherwise you may exhaust target card in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
        'oppositeOutput' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'upTo' => true,
          'targetLocation' => [RESERVE],
          'effect' => FT::ACTION(EXHAUST, []),
        ]),
      ],
      266 => [
        'description' => clienttranslate('Any number of target Characters in {V} gain 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'expeditionAttributes' => [FOREST],
          'n' => INFTY,
          'upTo' => true,
          'effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 2]),
        ]),
      ],
      268 => [
        'description' => clienttranslate('Cards other than me cost {1} more to play from Reserve.'),
        'noTrigger' => true,
        'attributes' => ['dynamicIncreaseReserveCost' => '1'],
      ],
      379 => [
        'description' => clienttranslate('Create a <BRASSBUG> Robot token in my Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => ['initialSource'],
        ]),
      ],
      381 => [
        'description' => clienttranslate('Create a <BRASSBUG> Robot token in your Companion Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => [STORM_RIGHT],
        ]),
      ],
      382 => [
        'description' => clienttranslate('Create a <BRASSBUG> Robot token in your Hero Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => [STORM_LEFT],
        ]),
      ],
      380 => [
        'description' => clienttranslate('Create a <BRASSBUG> Robot token in your other Expedition (the one I\'m not in).'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => ['oppositeSource'],
        ]),
      ],
      270 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in each of your Expeditions.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'YZ_Common_ManaMoth',
            'targetLocation' => [STORM_LEFT],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'YZ_Common_ManaMoth',
            'targetLocation' => [STORM_RIGHT],
            'moreThan1' => true,
          ])
        ),
      ],
      413 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in each of your Expeditions.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'YZ_Common_ManaMoth',
            'targetLocation' => [STORM_LEFT],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'YZ_Common_ManaMoth',
            'targetLocation' => [STORM_RIGHT],
            'moreThan1' => true,
          ])
        ),
      ],
      271 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in my Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => ['initialSource'],
        ]),
      ],
      414 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in my Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => ['initialSource'],
        ]),
      ],
      272 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in target Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => STORMS,
        ]),
      ],
      415 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in target Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => STORMS,
        ]),
      ],
      273 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in your Companion Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => [STORM_RIGHT],
        ]),
      ],
      416 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in your Companion Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => [STORM_RIGHT],
        ]),
      ],
      274 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in your Hero Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => [STORM_LEFT],
        ]),
      ],
      417 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in your Hero Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => [STORM_LEFT],
        ]),
      ],
      275 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in your other Expedition (the one I\'m not in).'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => ['oppositeSource'],
        ]),
      ],
      418 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in your other Expedition (the one I\'m not in).'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => ['oppositeSource'],
        ]),
      ],
      406 => [
        'description' => clienttranslate(
          'Create an <ORDIS_RECRUIT> Soldier token in each of your Expeditions, otherwise create one in my Expedition.'
        ),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => [STORM_RIGHT],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => [STORM_LEFT],
            'moreThan1' => true,
          ])
        ),
        'oppositeOutput' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => ['source'],
        ]),
      ],
      276 => [
        'description' => clienttranslate('Create one <ORDIS_RECRUIT> Soldier token in my Expedition per Bureaucrat you control.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'invokeOrdisRecruitBureaucrat']),
      ],
      401 => [
        'description' => clienttranslate('Create two <BRASSBUG> Robot tokens in target Expedition, otherwise create only one.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'n' => 2,
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => STORMS,
        ]),
        'oppositeOutput' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => STORMS,
        ]),
      ],
      277 => [
        'description' => clienttranslate('Create two <MANA_MOTH> Illusion tokens in target Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'n' => 2,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => STORMS,
        ]),
      ],
      399 => [
        'description' => clienttranslate('Draw a card, otherwise <RESUPPLY>.'),
        'output' => FT::ACTION(DRAW, ['players' => ME]),
        'oppositeOutput' => FT::ACTION(RESUPPLY, []),
      ],
      279 => [
        'description' => clienttranslate('Each Character in target Expedition gains 1 boost.'),
        'output' => FT::ACTION(TARGET_EXPEDITION, [
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostAllCharactersInExpedition']),
        ]),
      ],
      280 => [
        'description' => clienttranslate('Each Character you control other than me gains 1 boost.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'boostAllCharactersExceptSelf'],
        ],
      ],
      281 => [
        'description' => clienttranslate('Each player chooses a Character they control. It gains <ASLEEP>.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'eachPlayerAsleep'],
        ],
      ],
      377 => [
        'description' => clienttranslate('Each player exhausts a card in their Reserve.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerExhaust']),
      ],
      283 => [
        'description' => clienttranslate('Each player may put a card from their Hand in Reserve to draw a card.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerOptionalHandReserveDraw']),
      ],
      285 => [
        'description' => clienttranslate('Exchange target Card in your Reserve with a card from your Hand.'),
        'output' => FT::ACTION(EXCHANGE, ['targetType' => [PERMANENT, SPELL, CHARACTER]]),
      ],
      392 => [
        'description' => clienttranslate('Exchange target Card in your Reserve with a card from your Hand.'),
        'output' => FT::ACTION(EXCHANGE, ['targetType' => [PERMANENT, SPELL, CHARACTER]]),
      ],
      286 => [
        'description' => clienttranslate('Exchange target Character in your Reserve with a card from your Hand.'),
        'output' => FT::ACTION(EXCHANGE, []),
      ],
      287 => [
        'description' => clienttranslate('Exchange target Spell in your Reserve with a card from your Hand.'),
        'output' => FT::ACTION(EXCHANGE, ['targetType' => [SPELL]]),
      ],
      288 => ['description' => clienttranslate('Exhaust me.'), 'output' => FT::ACTION(EXHAUST, ['cardId' => ME])],
      290 => [
        'description' => clienttranslate('Exhaust up to two cards in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'upTo' => true,
          'n' => 2,
          'targetLocation' => [RESERVE],
          'effect' => FT::ACTION(EXHAUST, []),
        ]),
      ],
      291 => [
        'description' => clienttranslate('Exhausted Characters in Reserve remain exhausted during Morning.'),
        'attributes' => ['exhaustCharactersMorning' => true],
      ],
      431 => [
        'description' => clienttranslate('I am <DEFENDER>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicDefender' => 'fullDefender'],
      ],
      265 => [
        'description' => clienttranslate('I am <TOUGH_X>, where X is the number of exhausted cards in Reserve.'),
        'noTrigger' => true,
        'attributes' => ['dynamicTough' => 'exhaustedReserve'],
      ],
      427 => [
        'description' => clienttranslate('I can gain <FLEETING> even if I was already <FLEETING>.'),
        'noTrigger' => true,
        'attributes' => ['canAlwaysGainFleeting' => true],
      ],
      292 => [
        'description' => clienttranslate('I gain 1 boost and <FLEETING>.'),
        'output' => FT::SEQ(FT::GAIN(ME, BOOST), FT::GAIN(ME, FLEETING)),
      ],
      294 => [
        'description' => clienttranslate('I gain 1 boost per <FLEETING> Character you control.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXFleetingChar']),
      ],
      390 => [
        'description' => clienttranslate('I gain 1 boost per <FLEETING> Character you control.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXFleetingChar']),
      ],
      295 => [
        'description' => clienttranslate('I gain 1 boost per Expedition in {V}.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXForForest']),
      ],
      296 => [
        'description' => clienttranslate('I gain 2 boosts and <FLEETING>.'),
        'output' => FT::SEQ(FT::GAIN(ME, BOOST, 2), FT::GAIN(ME, FLEETING)),
      ],
      400 => [
        'description' => clienttranslate('I gain 2 boosts, otherwise I gain 1 boost.'),
        'output' => FT::GAIN(ME, BOOST, 2),
        'oppositeOutput' => FT::GAIN(ME, BOOST),
      ],
      297 => [
        'description' => clienttranslate('If I would gain <ASLEEP>, I gain <ANCHORED> instead.'),
        'noTrigger' => true,
        'attributes' => ['dynamicGainReplace' => [ASLEEP => ANCHORED]],
      ],
      298 => [
        'description' => clienttranslate('If I would gain <FLEETING>, I gain 1 boost instead.'),
        'noTrigger' => true,
        'attributes' => ['dynamicGainReplace' => [FLEETING => BOOST]],
      ],
      421 => [
        'description' => clienttranslate('If I would gain <FLEETING>, I gain 1 boost instead.'),
        'noTrigger' => true,
        'attributes' => ['dynamicGainReplace' => [FLEETING => BOOST]],
      ],
      422 => [
        'description' => clienttranslate('If the Expedition facing me is in {M}, it can only move forward due to {M}.'),
        'noTrigger' => true,
        'attributes' => ['opponentMountainOnly' => true],
      ],
      299 => [
        'description' => clienttranslate('If the Expedition facing me is in {O}, it can only move forward due to {O}.'),
        'noTrigger' => true,
        'attributes' => ['opponentOceanOnly' => true, 'blockMoveExpedition' => true],
      ],
      300 => [
        'description' => clienttranslate('If the Expedition facing me is in {V}, it can only move forward due to {V}.'),
        'noTrigger' => true,
        'attributes' => ['opponentForestOnly' => true],
      ],
      302 => [
        'description' => clienttranslate(
          'My region and the region of the Expedition facing me are {V} and lose their other types.'
        ),
        'attributes' => [
          'updateExpeditions' => ['type' => 'sourceAll', 'regionsRemove' => [OCEAN, MOUNTAIN], 'regionsAdd' => [FOREST]],
        ],
      ],
      303 => [
        'description' => clienttranslate('My region is {V} in addition to its other types.'),
        'attributes' => ['updateExpeditions' => ['type' => 'source', 'regionsAdd' => [FOREST]]],
      ],
      395 => [
        'description' => clienttranslate('My region is {V} in addition to its other types.'),
        'attributes' => ['updateExpeditions' => ['type' => 'source', 'regionsAdd' => [FOREST]]],
      ],
      306 => [
        'description' => clienttranslate('Ready all cards in your Reserve.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'readyAllReserve']),
      ],
      308 => [
        'description' => clienttranslate(
          'Roll a die, then target a Character. On a 4+, it gains <ANCHORED>. On a 1-3, it gains <ASLEEP>.'
        ),
        'output' =>
        FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1-3' => FT::ACTION(TARGET, [
              'effect' => FT::GAIN(EFFECT, ASLEEP),
            ]),
            '4+' => FT::ACTION(TARGET, [
              'effect' => FT::GAIN(EFFECT, ANCHORED),
            ]),
          ],
        ])
      ],
      420 => [
        'description' => clienttranslate('Sacrifice a Character in my Expedition and I gain 1 boost.'),
        'output' => FT::SEQ(
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER, TOKEN],
            'targetLocation' => ['source'],
            'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
          ]),
          FT::GAIN(ME, BOOST)
        ),
      ],
      311 => [
        'description' => clienttranslate('Sacrifice me.'),
        'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'desc' => 'sacrifice']),
      ],
      305 => [
        'description' => clienttranslate('Send me to Reserve.'),
        'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => RESERVE]),
      ],
      424 => [
        'description' => clienttranslate('Send me to Reserve.'),
        'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => RESERVE]),
      ],
      282 => [
        'description' => clienttranslate(
          'Starting with you, each player may immediately play a card with Hand Cost {3} or less for free.'
        ),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'playAll1Card'],
        ],
      ],
      318 => [
        'description' => clienttranslate(
          'Target a Character, then roll a die. On a 4+, it gains <ANCHORED>. On a 1-3, it gains <ASLEEP>.'
        ),
        'output' => FT::ACTION(TARGET, [
          'effect' => FT::ACTION(ROLL_DIE, [
            'effect' => [
              '1-3' => FT::GAIN(EFFECT, ASLEEP),
              '4+' => FT::GAIN(EFFECT, ANCHORED),
            ],
          ]),
        ]),
      ],
      319 => [
        'description' => clienttranslate('Target Character gains 1 boost and <FLEETING>.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::SEQ(FT::GAIN(EFFECT, FLEETING), FT::GAIN(EFFECT, BOOST))]),
      ],
      320 => [
        'description' => clienttranslate('Target Character gains 2 boosts and <FLEETING>.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::SEQ(FT::GAIN(EFFECT, FLEETING), FT::GAIN(EFFECT, BOOST, 2))]),
      ],
      323 => [
        'description' => clienttranslate('Target Character other than me gains 1 boost.'),
        'output' => FT::ACTION(TARGET, ['excludeSelf' => true, 'effect' => FT::GAIN(EFFECT, BOOST)]),
      ],
      429 => [
        'description' => clienttranslate('Target Character with Hand Cost {3} or less gains <ANCHORED>.'),
        'output' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'effect' => FT::GAIN(EFFECT, ANCHORED)]),
      ],
      428 => [
        'description' => clienttranslate('Target Character with Hand Cost {3} or less gains <ANCHORED>.'),
        'output' => FT::ACTION(TARGET, ['maxHandCost' => 3, 'effect' => FT::GAIN(EFFECT, ANCHORED)]),
      ],
      324 => [
        'description' => clienttranslate('Target Character you control with Hand Cost {4} or less gains <ASLEEP>.'),
        'output' => FT::ACTION(TARGET, [
          'maxHandCost' => 4,
          'targetPlayer' => ME,
          'excludedStatuses' => [ASLEEP],
          'effect' => FT::GAIN(EFFECT, ASLEEP),
        ]),
      ],
       325 => [
        // #206754: same leave-trigger issue as 537 — insert via SpecialEffect at runtime
        // instead of TARGET_PLAYER > SEQ_OPTIONAL > RESUPPLY.
        'description' => clienttranslate('Target opponent may <EXHAUSTED_RESUPPLY_INF> twice.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'targetOpponentOptionalExhaustedResupply',
          'args' => ['n' => 2],
        ]),
      ],
      396 => [
        'description' => clienttranslate('Target opponent may <EXHAUSTED_RESUPPLY_INF> twice.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'targetOpponentOptionalExhaustedResupply',
          'args' => ['n' => 2],
        ]),
      ],
      326 => [
        'description' => clienttranslate('Target opponent may <EXHAUSTED_RESUPPLY_INF>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'targetOpponentOptionalExhaustedResupply',
          'args' => ['n' => 1],
        ]),
      ],
      397 => [
        'description' => clienttranslate('Target opponent may <EXHAUSTED_RESUPPLY_INF>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'targetOpponentOptionalExhaustedResupply',
          'args' => ['n' => 1],
        ]),
      ],
      327 => [
        'description' => clienttranslate('Target player sacrifices a Character.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER, TOKEN],
            'n' => 1,
            'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
          ]),
        ]),
      ],
      407 => [
        'description' => clienttranslate('The next Bureaucrat you play this turn costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => BUREAUCRAT, 'reduction' => 1]],
        ],
      ],
      408 => [
        'description' => clienttranslate('The next card you play this turn costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => ALL, 'reduction' => 1]],
        ],
      ],
      387 => [
        'description' => clienttranslate('The next Character you play this Afternoon costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => CHARACTER, 'reduction' => 1, 'permanent' => true]],
        ],
      ],
      412 => [
        'description' => clienttranslate('The next Character you play this turn costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => CHARACTER, 'reduction' => 1, 'permanent' => false]],
        ],
      ],
      328 => [
        'description' => clienttranslate('The next Permanent you play this Afternoon costs {3} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 3, 'permanent' => true]],
        ],
      ],
      329 => [
        'description' => clienttranslate('The next Permanent you play this Afternoon costs {4} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 4, 'permanent' => true]],
        ],
      ],
      409 => [
        'description' => clienttranslate('The next Permanent you play this turn costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 1, 'permanent' => false]],
        ],
      ],
      410 => [
        'description' => clienttranslate('The next Plant you play this turn costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PLANT, 'reduction' => 1]],
        ],
      ],
      411 => [
        'description' => clienttranslate('The next Spell you play this turn costs {1} less.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => SPELL, 'reduction' => 1]],
        ],
      ],
      330 => [
        'description' => clienttranslate('You may give target <FLEETING> Character 1 boost.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'statuses' => FLEETING,
          'effect' => FT::GAIN(EFFECT, BOOST),
        ]),
      ],
      331 => [
        'description' => clienttranslate('You may give target <FLEETING> Character 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'statuses' => FLEETING,
          'effect' => FT::GAIN(EFFECT, BOOST, 2),
        ]),
      ],
      321 => [
        'description' => clienttranslate('You may give 2 boosts to target Character in {V}.'),
        'output' => FT::ACTION(TARGET, [
          'expeditionAttributes' => [FOREST],
          'upTo' => true,
          'effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 2]),
        ]),
      ],
      388 => [
        'description' => clienttranslate('You may give 2 boosts to target Character in {V}.'),
        'output' => FT::ACTION(TARGET, [
          'expeditionAttributes' => [FOREST],
          'upTo' => true,
          'effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 2]),
        ]),
      ],
      332 => [
        'description' => clienttranslate('You may have target Character or Expedition Permanent switch Expedition.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'effect' => FT::ACTION(MOVE_CARD, [])]),
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'upTo' => true,
            'subtype' => EXPEDITION,
            'effect' => FT::ACTION(MOVE_CARD, []),
          ])
        ),
      ],
      393 => [
        'description' => clienttranslate('You may have target Character or Expedition Permanent switch Expedition.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'effect' => FT::ACTION(MOVE_CARD, [])]),
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'upTo' => true,
            'subtype' => EXPEDITION,
            'effect' => FT::ACTION(MOVE_CARD, []),
          ])
        ),
      ],
      333 => [
        'description' => clienttranslate('You may have target Character switch Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'effect' => FT::ACTION(MOVE_CARD, []),
        ]),
      ],
      394 => [
        'description' => clienttranslate('You may have target Character switch Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'effect' => FT::ACTION(MOVE_CARD, []),
        ]),
      ],
      335 => [
        'description' => clienttranslate(
          'When a <BOOSTED> Character would leave my Expedition during the Afternoon, it loses its boosts instead.'
        ),
        'noTrigger' => true,
        'attributes' => ['protectBoostedInExpedition' => true],
      ],
      336 => [
        'description' => clienttranslate(
          'When an <ANCHORED> Character would leave my Expedition during the Afternoon, it loses <ANCHORED> instead.'
        ),
        'noTrigger' => true,
        'attributes' => ['protectAnchoredInExpedition' => true],
      ],
      337 => [
        'description' => clienttranslate(
          'You may exhaust target card in an opponent\'s Reserve, then roll a die. When you roll a 1-3 this way — That opponent targets a card in your Reserve. Exhaust it.'
        ),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetLocation' => [RESERVE],
          'targetPlayer' => OPPONENT,
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(EXHAUST, []),
            FT::ACTION(ROLL_DIE, [
              'effect' => [
                '1-3' => FT::ACTION(
                  TARGET,
                  [
                    'targetType' => [CHARACTER, SPELL, PERMANENT],
                    'targetLocation' => [RESERVE],
                    'targetPlayer' => OPPONENT,
                    'effect' => FT::ACTION(EXHAUST, [], ['pId' => 'nextPlayer']),
                  ],
                  ['pId' => 'nextPlayer']
                ),
              ],
            ])
          ),
        ]),
      ],
      289 => [
        'description' => clienttranslate(
          'You may exhaust target card in an opponent\'s Reserve, then roll a die. When you roll a 1-3 this way — That opponent targets a card in your Reserve. Exhaust it.'
        ),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1-3' => FT::ACTION(
              TARGET,
              [
                'targetType' => [CHARACTER, SPELL, PERMANENT],
                'targetLocation' => [RESERVE],
                'targetPlayer' => OPPONENT,
                'effect' => FT::ACTION(EXHAUST, []),
              ],
              ['pId' => 'nextPlayer']
            ),
          ],
        ]),
      ],
      338 => [
        'description' => clienttranslate('You may exhaust target card in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::ACTION(EXHAUST, []),
        ]),
      ],
      340 => [
        'description' => clienttranslate('You may have me gain <ASLEEP>.'),
        'output' => FT::SEQ_OPTIONAL(FT::GAIN(ME, ASLEEP)),
      ],
      341 => [
        'description' => clienttranslate('You may have target Character in the Expedition facing me gain <ASLEEP>.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => OPPONENT,
          'upTo' => true,
          'targetLocation' => ['source'],
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::GAIN(EFFECT, ASLEEP),
        ]),
      ],
      342 => [
        'description' => clienttranslate('You may immediately play a Character for {3} less.'),
        'output' => FT::SEQ_OPTIONAL(
          [
            'action' => SPECIAL_EFFECT,
            'args' => ['effect' => 'costReduction', 'args' => ['type' => CHARACTER, 'reduction' => 3, 'permanent' => false]],
          ],
          FT::ACTION(CHOOSE_ASSIGNMENT, ['types' => [CHARACTER], 'actions' => ['play']])
        ),
      ],
      343 => [
        'description' => clienttranslate('You may immediately play a Character for {3} less. It gains <ANCHORED>.'),
        'output' => FT::SEQ_OPTIONAL(
          [
            'action' => SPECIAL_EFFECT,
            'args' => ['effect' => 'costReduction', 'args' => ['type' => CHARACTER, 'reduction' => 3, 'permanent' => false]],
          ],
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterAnchored']),
          FT::ACTION(CHOOSE_ASSIGNMENT, ['types' => [CHARACTER], 'actions' => ['play']])
        ),
      ],
      344 => [
        'description' => clienttranslate('You may immediately play a Character for {3} less. It gains 1 boost.'),
        'output' => FT::SEQ_OPTIONAL(
          [
            'action' => SPECIAL_EFFECT,
            'args' => ['effect' => 'costReduction', 'args' => ['type' => CHARACTER, 'reduction' => 3, 'permanent' => false]],
          ],
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains1Boost']),
          FT::ACTION(CHOOSE_ASSIGNMENT, ['types' => [CHARACTER], 'actions' => ['play']])
        ),
      ],
      345 => [
        'description' => clienttranslate('You may immediately play a Character for {3} less. It gains 2 boosts.'),
        'output' => FT::SEQ_OPTIONAL(
          [
            'action' => SPECIAL_EFFECT,
            'args' => ['effect' => 'costReduction', 'args' => ['type' => CHARACTER, 'reduction' => 3, 'permanent' => false]],
          ],
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains2Boost']),
          FT::ACTION(CHOOSE_ASSIGNMENT, ['types' => [CHARACTER], 'actions' => ['play']])
        ),
      ],
      346 => [
        'description' => clienttranslate('You may play exhausted cards from your Reserve.'),
        'noTrigger' => true,
        'attributes' => ['playTappedAllCards' => true],
      ],
      391 => [
        'description' => clienttranslate('You may play exhausted cards from your Reserve.'),
        'noTrigger' => true,
        'attributes' => ['playTappedAllCards' => true],
      ],
      347 => [
        'description' => clienttranslate('You may play exhausted Characters from your Reserve.'),
        'noTrigger' => true,
        'attributes' => ['playTappedCharacters' => true],
      ],
      348 => [
        'description' => clienttranslate('You may ready an exhausted card in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetLocation' => [RESERVE],
          'isTapped' => true,
          'upTo' => true,
          'effect' => FT::ACTION(READY, []),
        ]),
      ],
      349 => [
        'description' => clienttranslate('You may return target Character or Permanent to its owner\'s hand.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'effect' => FT::RETURN_TO_HAND(),
        ]),
      ],
      350 => [
        'description' => clienttranslate('You may send target Character in {V} to Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'expeditionAttributes' => [FOREST],
          'upTo' => true,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      314 => [
        'description' => clienttranslate('You may send target Character to Reserve, then exhaust it.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'effect' => FT::SEQ(FT::DISCARD_TO_RESERVE(), FT::ACTION(EXHAUST, [])),
        ]),
      ],
      351 => [
        'description' => clienttranslate(
          'You may send target Character to Reserve. Unless it was in {V}, its controller draws a card.'
        ),
        'output' => FT::ACTION(TARGET, [
          'effect' => FT::SEQ(
            FT::DISCARD_TO_RESERVE(),
            FT::ACTION(CHECK_CONDITION, [
              'condition' => 'isDiscardedCardNotInBiome:forest',
              'effect' => FT::ACTION(DRAW, ['players' => OPPONENT]),
            ])
          ),
        ]),
      ],
      312 => [
        'description' => clienttranslate('You may send to Reserve any number of target Characters with total {M} of 4 or less.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'n' => INFTY,
          'totalMountain' => 4,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      313 => [
        'description' => clienttranslate('You may send to Reserve any number of target Characters with total {M} of 5 or less.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'n' => INFTY,
          'totalMountain' => 5,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      315 => [
        'description' => clienttranslate('You may send to Reserve target Character with no statistic over 3.'),
        'output' => FT::ACTION(TARGET, ['upTo' => true, 'maxStatistic' => 3, 'effect' => FT::DISCARD_TO_RESERVE()]),
      ],
      309 => [
        'description' => clienttranslate('Roll a die. On a 4+, <RESUPPLY_LOW>. On a 1-3, <EXHAUSTED_RESUPPLY_LOW>.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1-3' => FT::ACTION(RESUPPLY, ['exhausted' => true]),
            '4+' => FT::ACTION(RESUPPLY, []),
          ],
        ]),
      ],
      389 => [
        'description' => clienttranslate('Roll a die. On a 4+, <RESUPPLY_LOW>. On a 1-3, <EXHAUSTED_RESUPPLY_LOW>.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1-3' => FT::ACTION(RESUPPLY, ['exhausted' => true]),
            '4+' => FT::ACTION(RESUPPLY, []),
          ],
        ]),
      ],
      284 => [
        'description' => clienttranslate('Each player may put a card from their Hand in Reserve.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerOptionalHandReserve']),
      ],
      267 => [
        'description' => clienttranslate(
          'Cards other than me cost {1} less to play from Reserve. This effect can\'t make them cost less than {1}.'
        ),
        'noTrigger' => true,
        'attributes' => ['dynamicReduceReserveCost' => '1', 'dynamicMinimumReserveCost' => '1'],
      ],
      // Bise
      449 => [
        'description' => clienttranslate('<AUGMENT_IMP> any number of target cards in play or in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT, TOKEN],
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE],
          'upTo' => true,
          'n' => INFTY,
          'augmentOnly' => true,
          'effect' => FT::AUGMENT(EFFECT),
        ]),
      ],
      450 => [
        'description' => clienttranslate('<SABOTAGE> after Rest.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'AfterRestSabotage']),
      ],
      74 => [
        'description' => clienttranslate('<SABOTAGE> after Rest.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'AfterRestSabotage']),
      ],
      451 => [
        'description' => clienttranslate('<SABOTAGE>, otherwise <RESUPPLY>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
        'oppositeOutput' => FT::ACTION(RESUPPLY, []),
      ],
      452 => [
        'description' => clienttranslate('<SABOTAGE>. If you discarded a card this way, its owner <EXHAUSTED_RESUPPLIES>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::SEQ(FT::ACTION(DISCARD, []), FT::ACTION(RESUPPLY, ['player' => 'owner', 'exhausted' => true])),
        ]),
      ],
      453 => ['description' => clienttranslate('<SCOUT_1> {1}.'), 'attributes' => ['scout' => 1]],
      454 => ['description' => clienttranslate('<SCOUT_2> {2}.'), 'attributes' => ['scout' => 2]],
      455 => ['description' => clienttranslate('<SCOUT_3> {3}.'), 'attributes' => ['scout' => 3]],
      456 => ['description' => clienttranslate('<SCOUT_4> {4}.'), 'attributes' => ['scout' => 4]],
      529 => [
        'description' => clienttranslate(
          '{H} Discard all other cards from play and Reserve. When a card goes to the discard pile — I gain 1 boost.'
        ),
        'output' => '',
      ], // Hardcoded
      457 => [
        'description' => clienttranslate('Each Animal you control other than me gains 1 boost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'boostAllSubtype',
          'args' => ['excludeSelf' => true, 'subType' => ANIMAL],
        ]),
      ],
      458 => [
        'description' => clienttranslate(
          'Artists other than me cost {1} less to play from your Reserve. This effect can\'t make them cost less than {1}.'
        ),
        'attributes' => ['dynamicReduceReserveCost' => 'myArtist'],
      ],
      459 => [
        'description' => clienttranslate('Each Artist you control other than me gains 1 boost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'boostAllSubtype',
          'args' => ['excludeSelf' => true, 'subType' => ARTIST],
        ]),
      ],
      460 => [
        'description' => clienttranslate('Cards in play or in Reserve that already have counters can\'t gain more.'),
        'attributes' => ['blockGainNewCounters' => true],
      ],
      461 => [
        'description' => clienttranslate('Cards in your opponent\'s Reserve can\'t gain counters.'),
        'attributes' => ['blockOpponentReserveGain' => true],
      ],
      462 => [
        'description' => clienttranslate('Characters in your Reserve gain 1 boost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostReserve']),
      ],
      463 => [
        'description' => clienttranslate(
          'Characters other than me cost {1} less to play from your Reserve. This effect can\'t make them cost less than {1}.'
        ),
        'attributes' => ['dynamicReduceReserveCost' => 'myCharacter'],
      ],
      464 => [
        'description' => clienttranslate(
          'Choose one: • I gain 1 boost, up to a max of 3. • {T} : I gain 2 boosts, up to a max of 3.'
        ),
        'output' => FT::XOR(
          FT::ACTION(GAIN, ['cardId' => ME, 'type' => BOOST, 'upTo' => 3]),
          FT::SEQ(FT::ACTION(TAP, []), FT::ACTION(GAIN, ['cardId' => ME, 'type' => BOOST, 'n' => 2, 'upTo' => 3]))
        ),
      ],
      465 => [
        'description' => clienttranslate('Create two <ORDIS_RECRUIT> Soldier tokens distributed among any Expeditions.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => STORMS,
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => STORMS,
            'moreThan1' => true,
          ])
        ),
      ],
      466 => [
        'description' => clienttranslate(
          'Create two <ORDIS_RECRUIT> Soldier tokens in your other Expedition (the one I\'m not in).'
        ),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => ['oppositeSource'],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => ['oppositeSource'],
            'moreThan1' => true,
          ])
        ),
      ],
      467 => [
        'description' => clienttranslate(
          'Create two <ORDIS_RECRUIT> Soldier tokens in your other Expedition (the one I\'m not in).'
        ),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => ['oppositeSource'],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => CONTROLLER,
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => ['oppositeSource'],
            'moreThan1' => true,
          ])
        ),
      ],
      468 => [
        'description' => clienttranslate('Discard all other cards in play and in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'n' => INFTY,
          'targetLocation' => [STORM_RIGHT, STORM_LEFT, LANDMARK, RESERVE],
          'targetType' => [TOKEN, CHARACTER, SPELL, PERMANENT],
          'excludeSelf' => true,
          'ignoreTough' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      469 => [
        'description' => clienttranslate('Distribute 3 boosts among any Characters in Reserve.'),
        'output' => FT::SEQ(
          FT::ACTION(TARGET, ['targetLocation' => [RESERVE], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
          FT::ACTION(TARGET, ['targetLocation' => [RESERVE], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
          FT::ACTION(TARGET, ['targetLocation' => [RESERVE], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])])
        ),
      ],
      470 => [
        'description' => clienttranslate('Double the number of boosts on each Character you control and in your Reserve.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'doubleBoosts']),
      ],
      524 => [
        'description' => clienttranslate('Draw two cards, otherwise draw a card.'),
        'output' => FT::ACTION(DRAW, ['players' => ME, 'n' => 2]),
        'oppositeOutput' => FT::ACTION(DRAW, ['players' => ME]),
      ],
      523 => [
        'description' => clienttranslate('Each Character in target Expedition gains <FLEETING>.'),
        'output' => FT::ACTION(TARGET_EXPEDITION, [
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'fleetingAllCharactersInExpedition']),
        ]),
      ],
      477 => [
        'description' => clienttranslate('I am <ETERNAL>. At Night — I lose 1 boost.'),
        'noTrigger' => true,
        'attributes' => ['dynamicEternal' => '1'],
        'passive' => [
          'AfterDusk' => [
            'conditions' => ['isMe', 'hasBoost'],
            'output' => FT::LOOSE(ME, BOOST),
          ],
        ],
      ],
      471 => [
        'description' => clienttranslate('I don\'t count towards your Reserve limit.'),
        'attributes' => ['ignoreReserveLimit' => true],
      ],
      472 => [
        'description' => clienttranslate('I gain 1 boost per card in your hand.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostHandCards']),
      ],
      525 => [
        'description' => clienttranslate('I gain 1 boost per card in your Reserve, otherwise I gain 1 boost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostReserveCards']),
        'oppositeOutput' => FT::GAIN(ME, BOOST),
      ],
      473 => [
        'description' => clienttranslate(
          'I gain 1 boost per Character other than me in my Expedition, then send them to Reserve.'
        ),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostAndRemoveFromExpedition']),
      ],
      474 => [
        'description' => clienttranslate('I gain 1 boost per Mana Orb in your Mana Zone.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXMana']),
      ],
      475 => ['description' => clienttranslate('I gain 1 boost, up to a max of 2.'), 'output' => FT::GAIN(ME, BOOST, 1, 2)],
      476 => ['description' => clienttranslate('I gain 1 boost, up to a max of 3.'), 'output' => FT::GAIN(ME, BOOST, 1, 3)],
      478 => [
        'description' => clienttranslate(
          'If there are no Characters in the Expedition facing me, that Character switches Expeditions.'
        ),
        'passive' => [
          'ChooseAssignment' => [
            'conditions' => ['isOpponentExpeditionEmpty', 'isNotPlayedInSameLocation'],
            'output' => FT::ACTION(MOVE_CARD, ['cardId' => EFFECT]),
          ],
        ],
      ],
      479 => [
        'description' => clienttranslate('Play a card with Hand Cost {3} or less for free.'),
        'output' => FT::ACTION(CHOOSE_ASSIGNMENT, ['actions' => ['play'], 'maxHandCost' => 3, 'free' => true]),
      ],
      480 => [
        'description' => clienttranslate('Ready two Mana Orbs.'),
        'output' => FT::SEQ(FT::ACTION(READY, ['cardId' => MANA]), FT::ACTION(READY, ['cardId' => MANA])),
      ],
      481 => [
        'description' => clienttranslate('Remove up to 2 counters from target card in play or in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT, TOKEN],
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE],
          'upTo' => true,
          'augmentOnly' => true,
          'effect' => FT::SEQ(
            FT::SEQ_OPTIONAL_MANUAL(FT::ACTION(LOOSE, ['upTo' => true, 'type' => 'counter'], ['optional' => true])),
            FT::SEQ_OPTIONAL_MANUAL(FT::ACTION(LOOSE, ['upTo' => true, 'type' => 'counter'], ['optional' => true]))
          ),
        ]),
      ],
      482 => [
        'description' => clienttranslate(
          'Reveal the top card of your deck. If it\'s a Character with a base statistic of 0, draw it, otherwise discard it.'
        ),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostedRevealBaseStat', 'args' => ['bypass' => true]]),
      ],
      483 => [
        'description' => clienttranslate(
          'Reveal the top card of your deck. If it\'s a Robot or Permanent, draw it, otherwise discard it.'
        ),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostedRevealRobotPermanent', 'args' => ['bypass' => true]]),
      ],
      484 => [
        'description' => clienttranslate(
          'Reveal the top card of your deck. If it\'s an Artist or Song, draw it, otherwise discard it.'
        ),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostedRevealArtistSong', 'args' => ['bypass' => true]]),
      ],
      485 => [
        'description' => clienttranslate(
          'Robots other than me cost {1} less to play from your Reserve. This effect can\'t make them cost less than {1}.'
        ),
        'attributes' => ['dynamicReduceReserveCost' => 'myRobot'],
      ],
      486 => [
        'description' => clienttranslate(
          'Target a Character other than me in play or in Reserve. Then, roll a die: • On a 4+, we both gain 1 boost. • On a 1-3, it gains 1 boost.'
        ),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE],
          'excludeSelf' => true,
          'effect' => FT::ACTION(ROLL_DIE, [
            'effect' => [
              '1-3' => FT::GAIN(EFFECT, BOOST),
              '4+' => FT::SEQ(FT::GAIN(EFFECT, BOOST), FT::GAIN(ME, BOOST)),
            ],
          ]),
        ]),
      ],
      487 => [
        'description' => clienttranslate(
          'Target a Character other than me in Reserve. Then, roll a die: • On a 4+, we both gain 1 boost. • On a 1-3, it gains 1 boost.'
        ),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'effect' => FT::ACTION(ROLL_DIE, [
            'effect' => [
              '1-3' => FT::GAIN(EFFECT, BOOST),
              '4+' => FT::SEQ(FT::GAIN(EFFECT, BOOST), FT::GAIN(ME, BOOST)),
            ],
          ]),
        ]),
      ],
      488 => [
        'description' => clienttranslate('Target Character in play or in Reserve gains 1 boost.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE, STORM_LEFT, STORM_RIGHT],
          'effect' => FT::GAIN(EFFECT, BOOST),
        ]),
      ],
      489 => [
        'description' => clienttranslate('Target Character in play or in Reserve gains 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE, STORM_LEFT, STORM_RIGHT],
          'effect' => FT::GAIN(EFFECT, BOOST, 2),
        ]),
      ],
      490 => [
        'description' => clienttranslate('Target Character in Reserve gains 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'effect' => FT::GAIN(EFFECT, BOOST, 2),
        ]),
      ],
      491 => [
        'description' => clienttranslate('Target Character in Reserve gains 3 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'effect' => FT::GAIN(EFFECT, BOOST, 3),
        ]),
      ],
      492 => [
        'description' => clienttranslate('Target Character other than me in play or in Reserve gains 1 boost.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE, STORM_LEFT, STORM_RIGHT],
          'excludeSelf' => true,
          'effect' => FT::GAIN(EFFECT, BOOST),
        ]),
      ],
      493 => [
        'description' => clienttranslate(
          'The {V}, {M}, and {O} of Characters you control other than me are equal to their highest statistic.'
        ),
        'attributes' => ['increaseAllOtherCharactersBiomesHighest' => true],
      ],
      527 => [
        'description' => clienttranslate('You may activate the {j} abilities of target Character you control other than me.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'targetPlayer' => ME,
          'hasEffects' => ['Played'],
          'excludeSelf' => true,
          'upTo' => true,
          'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
        ]),
      ],
      494 => [
        'description' => clienttranslate('Up to two target Characters in play or in Reserve each gain 1 boost.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'n' => 2,
          'targetLocation' => [RESERVE, STORM_LEFT, STORM_RIGHT],
          'effect' => FT::GAIN(EFFECT, BOOST),
        ]),
      ],
      495 => [
        'description' => clienttranslate('You may <AUGMENT> target card in play or in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT, TOKEN],
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE],
          'upTo' => true,
          'augmentOnly' => true,
          'effect' => FT::AUGMENT(EFFECT),
        ]),
      ],
      496 => [
        'description' => clienttranslate('You may discard target Character in play or in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, RESERVE],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      497 => [
        'description' => clienttranslate('You may discard target Character or Permanent with Hand Cost {2} or less.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'maxHandCost' => 2,
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      518 => [
        'description' => clienttranslate('You may discard target Character or Permanent with Hand Cost {2} or less.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'maxHandCost' => 2,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      498 => [
        'description' => clienttranslate(
          'You may discard target Character with Hand Cost {5} or less. If you do, create a <MANA_MOTH> Illusion token in its Expedition.'
        ),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'maxHandCost' => 5,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => CONTROLLER,
              'tokenType' => 'YZ_Common_ManaMoth',
              'targetLocation' => ['discardedSource'],
            ])
          ),
        ]),
      ],
      530 => [
        'description' => clienttranslate('You may discard target Character, otherwise send it to Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
        'oppositeOutput' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      499 => [
        'description' => clienttranslate('You may discard target Permanent with Hand Cost {2} or less.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'upTo' => true,
          'maxHandCost' => 2,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      500 => [
        'description' => clienttranslate(
          'You may discard target Permanent with Hand Cost {2} or less. If you do, its controller <RESUPPLIES>.'
        ),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'upTo' => true,
          'maxHandCost' => 2,
          'effect' => FT::SEQ(FT::ACTION(DISCARD, []), FT::ACTION(RESUPPLY, ['player' => 'owner'])),
        ]),
      ],
      501 => [
        'description' => clienttranslate('You may discard target Permanent with Hand Cost {3} or less.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'upTo' => true,
          'maxHandCost' => 3,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      502 => [
        'description' => clienttranslate('You may discard target Permanent with Hand Cost {4} or less.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'upTo' => true,
          'maxHandCost' => 4,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      503 => [
        'description' => clienttranslate('You may discard target Permanent with Hand Cost {5} or less.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'upTo' => true,
          'maxHandCost' => 5,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      504 => [
        'description' => clienttranslate('You may pay {1} to give it 1 boost.'),
        'output' => FT::SEQ_OPTIONAL_MANUAL(FT::ACTION(PAY, ['pay' => 1]), FT::GAIN(EFFECT, BOOST)),
      ],
      505 => [
        'description' => clienttranslate('You may put a card from your Reserve in your Mana zone (as an exhausted Mana Orb).'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'targetType' => [TOKEN, CHARACTER, PERMANENT, SPELL],
          'targetPlayer' => ME,
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, [
            'destination' => MANA,
            'tapped' => true,
            'force' => true,
          ]),
        ]),
      ],
      528 => [
        'description' => clienttranslate('You may put a card from your Reserve in your Mana zone as a ready Mana Orb.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'targetType' => [TOKEN, CHARACTER, PERMANENT, SPELL],
          'targetPlayer' => ME,
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, [
            'destination' => MANA,
            'tapped' => false,
            'force' => true,
          ]),
        ]),
      ],
      506 => [
        'description' => clienttranslate('You may return target Character to its owner\'s hand.'),
        'output' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'effect' => FT::RETURN_TO_HAND()]),
      ],
      507 => [
        'description' => clienttranslate('You may send to Reserve target Character with Hand Cost {1} or less.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'maxHandCost' => 1,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      508 => [
        'description' => clienttranslate('You may send to Reserve target Character with Hand Cost {2} or less.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'maxHandCost' => 2,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      531 => [
        'description' => clienttranslate('You may give 1 boost to target Character in play or in Reserve other than me.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, RESERVE],
          'excludeSelf' => true,
          'effect' => FT::GAIN(EFFECT, BOOST),
        ]),
      ],
      533 => [
        'description' => clienttranslate('You may have target Character other than me lose <FLEETING> and gain 1 boost.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'excludeSelf' => true,
          'effect' => FT::SEQ(FT::LOOSE(EFFECT, FLEETING), FT::GAIN(EFFECT, BOOST)),
        ]),
      ],
      534 => [
        'description' => clienttranslate('You may have target Character other than me lose <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'excludeSelf' => true,
          'effect' => FT::LOOSE(EFFECT, FLEETING),
        ]),
      ],
      535 => [
        'description' => clienttranslate('All Reserve limits are reduced by one.'),
        'attributes' => ['allReserveSlots' => -1],
      ],
      536 => [
        'description' => clienttranslate('I gain 1 boost per <BOOSTED> Character you control.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXBoostedChar']),
      ],
      537 => [
        // 206754: resupply the opponent directly (same as outputs 724/726) rather than
        // wrapping in TARGET_PLAYER. TARGET_PLAYER > RESUPPLY breaks on leave-style triggers
        // (11/12) where the source card is the event card, because Resupply::getPlayer()'s
        // event-pId fallback then overrides the target and the controller resupplies instead.
        'description' => clienttranslate('Target opponent <RESUPPLIES>.'),
        'output' => FT::ACTION(RESUPPLY, ['player' => 'nextPlayer']),
      ],
      539 => [
        'description' => clienttranslate('I gain 1 boost per Character target opponent controls.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'counterPerOpponentCharacter']),
      ],
      541 => [
        'description' => clienttranslate('Target Character in Reserve gains 1 boost.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'effect' => FT::GAIN(EFFECT, BOOST),
        ]),
      ],
      676 => [
        'description' => clienttranslate('You may have target Character other than me gain <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'excludeSelf' => true,
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::GAIN(EFFECT, FLEETING),
        ]),
      ],
      677 => [
        'description' => clienttranslate('Target Character other than me gains 1 boost and <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'excludeSelf' => true,
          'effect' => FT::SEQ(FT::GAIN(EFFECT, FLEETING), FT::GAIN(EFFECT, BOOST)),
        ]),
      ],
      678 => [
        'description' => clienttranslate('Create an <BRASSBUG> Robot token in my Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'AX_Common_Brassbug',
          'targetLocation' => ['source'],
        ]),
      ],
      161 => [
        'description' => clienttranslate('Target Expedition moves forward one region.'),
        'output' => FT::ACTION(MOVE_EXPEDITION, []),
      ],
      // Cyclone
      550 => [
        'description' => clienttranslate('<EXHAUSTED_RESUPPLY>, otherwise my Expedition <ASCENDS>.'),
        'output' => FT::ACTION(RESUPPLY, ['exhausted' => true]),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      681 => ['description' => clienttranslate('<GIGANTIC>, <TOUGH_1>.'), 'attributes' => ['gigantic' => true, 'tough' => 1]],
      568 => ['description' => clienttranslate('<GIGANTIC>.  {J} I gain 1 boost per Character in the Expeditions facing me.')], // Hardcoded
      593 => [
        'description' => clienttranslate(
          '<GIGANTIC>.  {J} You may discard any number of cards from your Reserve to give me that many boosts.'
        ),
      ], // Hardcoded
      607 => [
        'description' => clienttranslate(
          '<GIGANTIC>.  {J} You may sacrifice any number of Permanents to give me that many boosts.'
        ),
        'output' => '',
      ],
      570 => [
        'description' => clienttranslate('<GIGANTIC>.  I\'m also considered a Spell, even when not in play.'),
        'attributes' => ['gigantic' => true, 'additionalType' => [SPELL]],
      ],
      650 => [
        'description' => clienttranslate('<GIGANTIC>.  I\'m also considered an Expedition Permanent, even when not in play.'),
        'attributes' => ['gigantic' => true, 'additionalType' => [PERMANENT]],
      ],
      659 => [
        'description' => clienttranslate('<RESUPPLY>, otherwise create an <AEROLITH> token in your Landmarks.'),
        'output' => FT::ACTION(RESUPPLY, []),
        'oppositeOutput' => [
          'action' => INVOKE_TOKEN,
          'automatic' => true,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
        ],
      ],
      671 => [
        'description' => clienttranslate('<RESUPPLY>, otherwise each player <RESUPPLIES>.'),
        'output' => FT::ACTION(RESUPPLY, []),
        'oppositeOutput' => FT::SEQ(FT::ACTION(RESUPPLY, []), FT::ACTION(RESUPPLY, ['player' => 'nextPlayer'])),
      ],
      551 => [
        'description' => clienttranslate('<RESUPPLY>, otherwise my Expedition <ASCENDS>.'),
        'output' => FT::ACTION(RESUPPLY, []),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      553 => [
        'description' => clienttranslate('<SABOTAGE>, otherwise my Expedition <ASCENDS>.'),
        'output' => FT::SABOTAGE(),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      552 => [
        'description' => clienttranslate(
          '<SABOTAGE>. If you discarded a card this way, create a <WOOLLYBACK> Animal token in the Companion Expedition of its owner.'
        ),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'MU_Common_Woollyback',
              'targetLocation' => [STORM_RIGHT],
              'targetPlayer' => 'owner',
            ]),
          )
        ]),
      ],
      579 => [
        'description' => clienttranslate(
          '<SEASONED>.  {H} Target a Character facing me. I gain its subtype and X boosts, where X equals its highest statistic.'
        ),
      ], // hardcoded
      554 => [
        'description' => clienttranslate('All Characters gain <FLEETING>.'),
        'output' => FT::ACTION(TARGET, ['n' => INFTY, 'effect' => FT::GAIN(EFFECT, FLEETING)]),
      ],
      555 => [
        'description' => clienttranslate('All Characters other than me gain <FLEETING>.'),
        'output' => FT::ACTION(TARGET, ['n' => INFTY, 'excludeSelf' => true, 'effect' => FT::GAIN(EFFECT, FLEETING)]),
      ],
      557 => [
        'description' => clienttranslate('Any number of target Characters other than me lose <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'n' => INFTY,
          'upTo' => true,
          'excludeSelf' => true,
          'effect' => FT::GAIN(EFFECT, FLEETING),
        ]),
      ],
      669 => [
        'description' => clienttranslate(
          'Create a <WOOLLYBACK> Animal token in target Expedition, otherwise create one in target opponent\'s Companion Expedition.'
        ),
        'output' => FT::ACTION(
          INVOKE_TOKEN,
          [
            'pId' => 'source',
            'tokenType' => 'MU_Common_Woollyback',
          ],
          ['automatic' => false]
        ),
        'oppositeOutput' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'MU_Common_Woollyback',
          'targetLocation' => [STORM_RIGHT],
          'targetPlayer' => OPPONENT,
        ]),
      ],
      558 => [
        'description' => clienttranslate('Create a <WOOLLYBACK> Animal token in target Expedition.'),
        'output' => FT::ACTION(
          INVOKE_TOKEN,
          [
            'pId' => 'source',
            'tokenType' => 'MU_Common_Woollyback',
          ],
          ['automatic' => false]
        ),
      ],
      559 => [
        'description' => clienttranslate(
          'Create a <WOOLLYBACK> Animal token in target opponent\'s Companion Expedition, then draw a card.'
        ),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'MU_Common_Woollyback',
            'targetLocation' => [STORM_RIGHT],
            'targetPlayer' => OPPONENT,
          ]),
          FT::ACTION(DRAW, ['players' => ME])
        ),
      ],
      560 => [
        'description' => clienttranslate('Create an <AEROLITH> token in each player\'s Landmarks.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Aerolith',
            'targetLocation' => [LANDMARK],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Aerolith',
            'targetLocation' => [LANDMARK],
            'targetPlayer' => OPPONENT,
            'moreThan1' => true,
          ])
        ),
      ],
      647 => [
        'description' => clienttranslate(
          'Create an <AEROLITH> token in target player\'s Landmarks, otherwise my Expedition <ASCENDS>.'
        ),
        'output' => [
          'action' => INVOKE_TOKEN,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK], 'allPlayers' => true],
        ],
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      561 => [
        'description' => clienttranslate('Create an <AEROLITH> token in target player\'s Landmarks.'),
        'output' => [
          'action' => INVOKE_TOKEN,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK], 'allPlayers' => true],
        ],
      ],
      658 => [
        'description' => clienttranslate('Create an <AEROLITH> token in your Landmarks, otherwise <RESUPPLY>.'),
        'output' => [
          'action' => INVOKE_TOKEN,
          'automatic' => true,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
        ],
        'oppositeOutput' => FT::ACTION(RESUPPLY, [])
      ],
      672 => [
        'description' => clienttranslate(
          'Create an <AEROLITH> token in your Landmarks, otherwise create one in each player\'s Landmarks.'
        ),
        'output' => [
          'action' => INVOKE_TOKEN,
          'automatic' => true,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
        ],
        'oppositeOutput' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Aerolith',
            'targetLocation' => [LANDMARK],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Aerolith',
            'targetLocation' => [LANDMARK],
            'targetPlayer' => OPPONENT,
            'moreThan1' => true,
          ])
        ),
      ],
      562 => [
        'description' => clienttranslate('Create an <AEROLITH> token in your Landmarks.'),
        'output' => [
          'action' => INVOKE_TOKEN,
          'automatic' => true,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
        ],
      ],
      563 => [
        'description' => clienttranslate('Create two <AEROLITH> tokens in your Landmarks, otherwise create only one.'),
        'output' => FT::SEQ(
          [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
          ],
          [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK], 'moreThan1' => true,],
          ]
        ),
        'oppositeOutput' => [
          'action' => INVOKE_TOKEN,
          'automatic' => true,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
        ],
      ],
      653 => [
        'description' => clienttranslate('Create two <AEROLITH> tokens in your Landmarks, otherwise create only one.'),
        'output' => FT::SEQ(
          [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
          ],
          [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK], 'moreThan1' => true,],
          ]
        ),
        'oppositeOutput' => [
          'action' => INVOKE_TOKEN,
          'automatic' => true,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
        ],
      ],
      649 => [
        'description' => clienttranslate('Create two <AEROLITH> tokens in your Landmarks.'),
        'output' => FT::SEQ(
          [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
          ],
          [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK], 'moreThan1' => true,],
          ]
        ),
      ],
      652 => [
        'description' => clienttranslate('Create two <AEROLITH> tokens in your Landmarks.'),
        'output' => FT::SEQ(
          [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
          ],
          [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK], 'moreThan1' => true,],
          ]
        ),
      ],
      670 => [
        'description' => clienttranslate('Draw a card, otherwise each player draws a card.'),
        'output' => FT::ACTION(DRAW, ['players' => ME]),
        'oppositeOutput' => FT::ACTION(DRAW, []),
      ],
      564 => [
        'description' => clienttranslate('Each Animal and Adventurer in your Reserve gains 1 boost.'),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostReserveSubtype', 'args' => ['subType' => ANIMAL]]),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostReserveSubtype', 'args' => ['subType' => ADVENTURER]])
        ),
      ],
      565 => [
        'description' => clienttranslate('Each Animal in your Reserve gains 1 boost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostReserveSubtype', 'args' => ['subType' => ANIMAL]]),
      ],
      556 => [
        'description' => clienttranslate('Each player passes.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'allPass']),
      ],
      566 => [
        'description' => clienttranslate('Exhaust target card in Reserve, otherwise my Expedition <ASCENDS>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetLocation' => [RESERVE],
          'effect' => FT::ACTION(EXHAUST, []),
        ]),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      567 => [
        'description' => clienttranslate('I cost {1} less for each of your Ascended Expeditions.'),
        'attributes' => ['dynamicCostReduction' => 'eachOwnerAscended'],
      ],
      682 => ['description' => clienttranslate('I gain <ANCHORED>.'), 'output' => FT::GAIN(ME, ANCHORED)],
      683 => ['description' => clienttranslate('I gain <ANCHORED>.'), 'output' => FT::GAIN(ME, ANCHORED)],
      569 => [
        'description' => clienttranslate('I gain 1 boost, otherwise my Expedition <ASCENDS>.'),
        'output' => FT::GAIN(ME, BOOST),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      572 => [
        'description' => clienttranslate(
          'If my Expedition would move forward during Dusk, roll a die:  • On a 4+: it moves forward one more region instead.  • On a 1: it doesn\'t move instead.'
        ),
        'attributes' => ['actionInsteadAdvance' => 'ErisRare'],
      ],
      571 => [
        'description' => clienttranslate(
          'If my Expedition would move forward during Dusk, roll a die. On a 4+: it moves forward one more region instead.'
        ),
        'attributes' => ['actionInsteadAdvance' => 'ErisCommon'],
      ],
      573 => [
        'description' => clienttranslate('My {V}, {M}, and {O} are equal to my highest statistic.'),
        'attributes' => ['dynamicIncreaseBiomeHighestSelf' => '1'],
      ],
      574 => [
        'description' => clienttranslate('My Expedition <ASCENDS>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      575 => [
        'description' => clienttranslate('My Expedition <ASCENDS>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      576 => [
        'description' => clienttranslate('Place a {V} Terrain Marker on target visible region.'),
        'output' => FT::ACTION(MARK_REGION, ['create' => true, 'regionType' => FOREST]),
      ],
      655 => [
        'description' => clienttranslate('Put the top card of your deck in your Mana zone (as an exhausted Mana Orb).'),
        'output' => FT::ACTION(DRAW_MANA, []),
      ],
      577 => [
        'description' => clienttranslate('Reveal the top card of your deck. If it\'s a Spell, draw it.'),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'revealTop']),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'drawRevealed', 'args' => ['type' => SPELL]])
        ),
      ],
      578 => [
        'description' => clienttranslate(
          'Reveal the top card of your deck. If it\'s a Spell, draw it. If it\'s a Permanent, put it in Reserve.'
        ),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'revealTop']),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'drawRevealed', 'args' => ['type' => SPELL, 'reserve' => PERMANENT]])
        ),
      ],
      680 => [
        'description' => clienttranslate('Sacrifice a Permanent.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [PERMANENT],
          'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
        ]),
      ],
      580 => [
        'description' => clienttranslate(
          'Target a Character in any Reserve with Reserve Cost {2} or less. Immediately play it for free.'
        ),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => [RESERVE],
          'maxReserveCost' => 2,
          'effect' => FT::ACTION(PLAY_CARD, ['stealOwnership' => true, 'free' => true]),
        ]),
      ],
      581 => [
        'description' => clienttranslate('Target Character gains 1 boost per card in your Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostTargetReserveCards']),
        ]),
      ],
      582 => [
        'description' => clienttranslate('Target Character gains 1 boost, otherwise my Expedition <ASCENDS>.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      673 => [
        'description' => clienttranslate('Target Character gains 2 boosts, otherwise it gains 1 boost.'),
        'output' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST, 'n' => 2])]),
        'oppositeOutput' => FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
      ],
      583 => [
        'description' => clienttranslate('Target Character with Hand Cost {1} or less defects.'),
        'output' => FT::ACTION(TARGET, ['maxHandCost' => 1, 'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect'])]),
      ],
      584 => [
        'description' => clienttranslate('Target Character with Hand Cost {2} or less defects.'),
        'output' => FT::ACTION(TARGET, ['maxHandCost' => 2, 'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect'])]),
      ],
      585 => [
        'description' => clienttranslate('Target Expedition <ASCENDS>.'),
        'output' => FT::ACTION(TARGET_EXPEDITION, ['effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend'])]),
      ],
      586 => [
        'description' => clienttranslate('Target Expedition <ASCENDS>.'),
        'output' => FT::ACTION(TARGET_EXPEDITION, ['effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend'])]),
      ],
      587 => [
        'description' => clienttranslate('Target opponent may immediately play a card with Base Cost {4} or more for free.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'effect' => FT::ACTION(
            CHOOSE_ASSIGNMENT,
            ['actions' => ['play'], 'minBaseCost' => 4, 'free' => true],
            ['optional' => true]
          ),
        ]),
      ],
      588 => [
        'description' => clienttranslate('Target opponent may immediately play a Spell for free.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'effect' => FT::ACTION(
            CHOOSE_ASSIGNMENT,
            ['actions' => ['play'], 'free' => true, 'types' => [SPELL]],
            ['optional' => true]
          ),
        ]),
      ],
      679 => [
        'description' => clienttranslate('Target Permanent you control activates its {j} abilities.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'targetPlayer' => ME,
          'hasEffects' => ['Played'],
          'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
        ]),
      ],
      589 => [
        'description' => clienttranslate('Target player becomes first player.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'switchPlayer']),
        ]),
      ],
      590 => [
        'description' => clienttranslate('Target player puts a card from their hand in Reserve.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'effect' => FT::DISCARD_TO_RESERVE(),
          ]),
        ]),
      ],
      591 => [
        'description' => clienttranslate('Target token Character defects.'),
        'output' => FT::ACTION(TARGET, [
          'onlyToken' => true,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect']),
        ]),
      ],
      651 => [
        'description' => clienttranslate('The next Spell you play this turn loses <FLEETING>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'removeFleetingSpellPlayed']),
      ],
      592 => [
        'description' => clienttranslate(
          'You may create a <WOOLLYBACK> Animal token in target opponent\'s Companion Expedition.'
        ),
        'output' => FT::ACTION(
          INVOKE_TOKEN,
          [
            'pId' => 'source',
            'tokenType' => 'MU_Common_Woollyback',
            'targetLocation' => [STORM_RIGHT],
            'targetPlayer' => OPPONENT,
          ],
          ['optional' => true]
        ),
      ],
      594 => [
        'description' => clienttranslate(
          'You may discard target Character. If you do, create an <AEROLITH> token in its controller\'s Landmarks.'
        ),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'NE_Common_Aerolith',
              'targetLocation' => [LANDMARK],
              'targetPlayer' => 'owner',
            ])
          ),
        ]),
      ],
      595 => [
        'description' => clienttranslate('You may discard target Permanent. If you don\'t, I gain 2 boosts.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, ['targetType' => [PERMANENT], 'effect' => FT::ACTION(DISCARD, [])]),
          FT::GAIN(ME, BOOST, 2)
        ),
      ],
      //TODO 596 => ['description' => clienttranslate('You may discard target Permanent. If you don\'t, or if you discard a Permanent you control this way, I gain 2 boosts.'), 'output' => ''],
      597 => [
        'description' => clienttranslate('You may give me 1 boost and have me lose <ASLEEP>.'),
        'output' => FT::SEQ_OPTIONAL(FT::GAIN(ME, BOOST), FT::LOOSE(ME, ASLEEP)),
      ],
      598 => [
        'description' => clienttranslate('You may have me lose <ASLEEP>.'),
        'output' => FT::ACTION(LOOSE, ['type' => ASLEEP, 'cardId' => ME], ['optional' => true]),
      ],
      599 => [
        'description' => clienttranslate('You may have me switch Expeditions.'),
        'output' => FT::ACTION(MOVE_CARD, ['cardId' => ME], ['optional' => true]),
      ],
      646 => [
        'description' => clienttranslate('You may have target Character gain <ASLEEP>. If it\'s facing me, <SABOTAGE>.'),
      ], // Hardcoded
      667 => [
        'description' => clienttranslate('You may have target Character other than me gain 1 boost.'),
        'output' => FT::ACTION(TARGET, ['upTo' => true, 'excludeSelf' => true, 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
      ],
      600 => [
        'description' => clienttranslate('You may have target non-Fleeting Character gains 2 boosts and <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'excludedStatuses' => [FLEETING],
          'upTo' => true,
          'effect' => FT::SEQ(FT::GAIN(EFFECT, FLEETING), FT::GAIN(EFFECT, BOOST, 2)),
        ]),
      ],
      604 => [
        'description' => clienttranslate('You may immediately pass.'),
        'output' => FT::ACTION(END_AFTERNOON, [], ['optional' => true]),
      ],
      601 => [
        'description' => clienttranslate('You may immediately play a Character with Base Cost {2} or less for free.'),
        'output' => FT::ACTION(
          CHOOSE_ASSIGNMENT,
          ['actions' => ['play'], 'maxBaseCost' => 2, 'free' => true, 'types' => [CHARACTER]],
          ['optional' => true]
        ),
      ],
      602 => [
        'description' => clienttranslate('You may immediately play a Character with Base Cost {3} or less for free.'),
        'output' => FT::ACTION(
          CHOOSE_ASSIGNMENT,
          ['actions' => ['play'], 'maxBaseCost' => 3, 'free' => true, 'types' => [CHARACTER]],
          ['optional' => true]
        ),
      ],
      657 => [
        'description' => clienttranslate('You may immediately play a Permanent with Base Cost {3} or less for free.'),
        'output' => FT::ACTION(
          CHOOSE_ASSIGNMENT,
          ['actions' => ['play'], 'maxBaseCost' => 3, 'free' => true, 'types' => [PERMANENT]],
          ['optional' => true]
        ),
      ],
      603 => [
        'description' => clienttranslate('You may move a {V}, {M} or {O} Terrain Marker from any region to my region.'),
        'output' => FT::XOR(
          FT::ACTION(MOVE_REGION_MARKER, ['markerType' => FOREST]),
          FT::ACTION(MOVE_REGION_MARKER, ['markerType' => OCEAN]),
          FT::ACTION(MOVE_REGION_MARKER, ['markerType' => MOUNTAIN])
        ),
      ],
      654 => [
        'description' => clienttranslate('You may play Permanents for {1} less if their Base Cost is {4} or more.'),
        'attributes' => ['reduceCostType' => [PERMANENT => ['minBaseCost' => 4, 'reduction' => 1]]],
      ],
      605 => [
        'description' => clienttranslate('You may play Spells for {1} less if their Base Cost is {4} or more.'),
        'attributes' => ['reduceCostType' => [SPELL => ['minBaseCost' => 4, 'reduction' => 1]]],
      ],
      606 => [
        'description' => clienttranslate('You may play Spells for {2} less if their Base Cost is {7} or more.'),
        'attributes' => ['reduceCostType' => [SPELL => ['minBaseCost' => 7, 'reduction' => 2]]],
      ],
      668 => [
        'description' => clienttranslate(
          'You may send target Character to Reserve, otherwise you may return it to its owner\'s hand.'
        ),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
        'oppositeOutput' => FT::ACTION(TARGET, ['upTo' => true, 'targetType' => [CHARACTER], 'effect' => FT::RETURN_TO_HAND()]),
      ],
      608 => [
        'description' => clienttranslate(
          'You may send target Character to Reserve. If you do, create an <AEROLITH> token in its controller\'s Landmarks.'
        ),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::DISCARD_TO_RESERVE(),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'NE_Common_Aerolith',
              'targetLocation' => [LANDMARK],
              'targetPlayer' => 'owner',
            ])
          ),
        ]),
      ],
      666 => [
        'description' => clienttranslate('You may send to Reserve target Character with Hand Cost {4} or less.'),
        'output' => FT::ACTION(TARGET, ['maxHandCost' => 4, 'upTo' => true, 'effect' => FT::DISCARD_TO_RESERVE()]),
      ],
      609 => [
        'description' => clienttranslate('You may send to Reserve up to two target Characters with Hand Cost {1} or less.'),
        'output' => FT::ACTION(TARGET, ['maxHandCost' => 1, 'n' => 2, 'upTo' => true, 'effect' => FT::DISCARD_TO_RESERVE()]),
      ],
      // Duster
      690 => [
        'description' => clienttranslate('<RESUPPLY>, then you may <RUSH>.'),
        'output' => FT::SEQ(
          FT::ACTION(RESUPPLY, []),
          FT::RUSH_OPTIONAL(),
        )
      ],
      692 => [
        'description' => clienttranslate('Create a <MANASEED> token in each player\'s Landmarks.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Manaseed',
            'targetLocation' => [LANDMARK],
          ]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Manaseed',
            'targetLocation' => [LANDMARK],
            'targetPlayer' => OPPONENT
          ]),
        ),
      ],
      693 => [
        'description' => clienttranslate('Create a <MANASEED> token in target opponent\'s Landmarks.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'targetPlayer' => OPPONENT,
          'tokenType' => 'NE_Common_Manaseed',
          'targetLocation' => [LANDMARK],
        ]),
      ],
      694 => [
        'description' => clienttranslate('Create a <MANASEED> token in target player\'s Landmarks.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'NE_Common_Manaseed',
          'targetLocation' => [LANDMARK],
          'allPlayers' => true,
        ]),
      ],
      695 => [
        'description' => clienttranslate('Create three <MANASEED> tokens in your Landmarks.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'n' => 3,
          'tokenType' => 'NE_Common_Manaseed',
          'targetLocation' => [LANDMARK],
        ]),
      ],
      696 => [
        'description' => clienttranslate('Create two <MANASEED> tokens in your Landmarks.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'n' => 2,
          'tokenType' => 'NE_Common_Manaseed',
          'targetLocation' => [LANDMARK],
        ]),
      ],
      698 => [
        'description' => clienttranslate('Draw a card, then create two <MANASEED> tokens in your Landmarks.'),
        'output' => FT::SEQ(
          FT::ACTION(DRAW, ['players' => ME]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'n' => 2,
            'tokenType' => 'NE_Common_Manaseed',
            'targetLocation' => [LANDMARK],
          ]),
        )
      ],
      700 => [
        'description' => clienttranslate('Draw a card, then create a <MANASEED> token in your Landmarks.'),
        'output' => FT::SEQ(
          FT::ACTION(DRAW, ['players' => ME]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Manaseed',
            'targetLocation' => [LANDMARK],
          ]),
        )
      ],
      701 => [
        'description' => clienttranslate('Draw a card, then put a card from your hand in Reserve.'),
        'output' => FT::SEQ(
          FT::ACTION(DRAW, ['players' => ME]),
          FT::ACTION(TARGET, [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'effect' => FT::DISCARD_TO_RESERVE(),
          ])
        ),
      ],
      702 => [
        'description' => clienttranslate('Each player <RESUPPLIES>.'),
        'output' => FT::SEQ(
          FT::ACTION(RESUPPLY, []),
          FT::ACTION(RESUPPLY, ['player' => 'nextPlayer'])
        )
      ],
      703 => [
        'description' => clienttranslate('Each player exhausts a Permanent they control or a card in their Reserve.'),
        'output' => FT::PAR(
          FT::XOR(
            FT::ACTION(TARGET, [
              'targetType' => [PERMANENT],
              'targetPlayer' => ME,
              'isNotTapped' => true,
              'effect' => FT::SEQ(
                FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
              )
            ]),
            FT::ACTION(TARGET, [
              'targetType' => [SPELL, CHARACTER, PERMANENT],
              'targetPlayer' => ME,
              'isNotTapped' => true,
              'targetLocation' => [RESERVE],
              'effect' => FT::SEQ(
                FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
              )
            ])
          ),
          [
            'type' => NODE_XOR,
            'pId' => 'nextPlayer',
            'childs' => [
              FT::ACTION(TARGET, [
                'targetType' => [PERMANENT],
                'targetPlayer' => ME,
                'isNotTapped' => true,
                'effect' => FT::SEQ(
                  FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
                )
              ]),
              FT::ACTION(TARGET, [
                'targetType' => [SPELL, CHARACTER, PERMANENT],
                'targetPlayer' => ME,
                'isNotTapped' => true,
                'targetLocation' => [RESERVE],
                'effect' => FT::SEQ(
                  FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
                )
              ])
            ]
          ]
        )
      ],
      704 => [
        'description' => clienttranslate('Exhaust a Permanent you control or a card in your Reserve.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'targetPlayer' => ME,
            'isNotTapped' => true,
            'effect' => FT::SEQ(
              FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
            )
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [SPELL, CHARACTER, PERMANENT],
            'targetPlayer' => ME,
            'isNotTapped' => true,
            'targetLocation' => [RESERVE],
            'effect' => FT::SEQ(
              FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
            )
          ])
        )
      ],
      706 => [
        'description' => clienttranslate('My Expedition moves backwards one region. If it does, your other Expedition moves forward one region.'),
        'output' => FT::ACTION(MOVE_EXPEDITION, ['n' => -1, 'moveOtherExpedition' => true,])
      ],
      710 => [
        'description' => clienttranslate('An Expedition facing mine moves backwards one region. If it does, its controller moves their other Expedition forward one region.'),
        'output' => FT::ACTION(MOVE_EXPEDITION, ['expedition' => [EFFECT], 'n' => -1, 'moveOtherExpedition' => true,])
      ],
      707 => [
        'description' => clienttranslate('Play me for {1} less if you control an exhausted Permanent, and also {1} less if there\'s an exhausted card in your Reserve.'),
        'noTrigger' => true,
        'attributes' => [
          'dynamicCostReduction' => ['1:hasControl:permanent:1:false:exhausted', '1:hasReserve::::GTE:exhausted'],
        ]
      ],
      708 => [
        'description' => clienttranslate('You may play me for {1} less in an Expedition that\'s <IN_CONTACT>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicCostReduction' => '1:hasOneContact']
      ],
      709 => [
        'description' => clienttranslate('You may play me for {2} less in an Expedition that\'s <IN_CONTACT>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicCostReduction' => '2:hasOneContact']
      ],
      713 => [
        'description' => clienttranslate('I gain 1 boost per card in your Landmarks, up to a max of 3 boosts on me.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXLandmarkMax3'])
      ],
      714 => [
        'description' => clienttranslate('If I would go to Reserve from the Expedition zone, I gain <FLEETING> and defect instead.'),
        'noTrigger' => true,
        'attributes' => ['leaveExpeditionDefect' => true,]
      ],
      715 => [
        'description' => clienttranslate('Return another Character from your Reserve to your hand.'),
        'output' =>  FT::ACTION(
          TARGET,
          [
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'targetPlayer' => ME,
            'targetType' => [CHARACTER],
            'excludeSelf' => true,
            'effect' => FT::RETURN_TO_HAND(),
          ],
        ),
      ],
      716 => [
        'description' => clienttranslate('Target opponent reveals a random card from their hand. You may immediately play it for free or discard it.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'PhoibosUnique'])
      ],
      718 => [
        'description' => clienttranslate('You may send to Reserve target Character with Base Cost {3} or less.'),
        'output' =>  FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'upTo' => true,
          'maxBaseCost' => 3,
          'effect' => FT::DISCARD_TO_RESERVE()
        ])
      ],
      724 => [
        'description' => clienttranslate('Target opponent <EXHAUSTED_RESUPPLIES>.'),
        'output' => FT::ACTION(RESUPPLY, ['player' => 'nextPlayer', 'exhausted' => true])
      ],
      725 => [
        'description' => clienttranslate('Target opponent draws a card. Then, create a <MANASEED> token in their Landmarks.'),
        'output' => FT::SEQ(
          FT::ACTION(DRAW, ['players' => OPPONENT]),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'targetPlayer' => OPPONENT,
            'tokenType' => 'NE_Common_Manaseed',
            'targetLocation' => [LANDMARK],
          ]),
        )
      ],
      726 => [
        'description' => clienttranslate('Target player <EXHAUSTED_RESUPPLIES>.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => false,
          'effect' => FT::ACTION(RESUPPLY, ['exhausted' => true]),
        ]),
      ],
      728 => [
        'description' => clienttranslate('Pay {1} less for the next card you play this turn, down to a minimum of {1}.'),
        'output' =>  [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => ALL, 'reduction' => 1, 'minimum' => 1, 'permanent' => false]],
        ],
      ],
      729 => [
        'description' => clienttranslate('The next Character played from your hand this turn activates one of its {r} abilities.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'triggerEffectOfNextCharacter',
          'args' => ['type' => CHARACTER, 'from' => HAND, 'limit' => 1, 'effect' => RESERVE],
        ]),
      ],
      730 => [
        'description' => clienttranslate('Pay {1} less for the next Character you play this turn, down to a minimum of {1}.'),
        'output' =>  [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => CHARACTER, 'reduction' => 1, 'minimum' => 1, 'permanent' => false]],
        ],
      ],
      731 => [
        'description' => clienttranslate('Pay {1} less for the next Permanent you play this turn, down to a minimum of {1}.'),
        'output' =>  [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 1, 'minimum' => 1, 'permanent' => false]],
        ],
      ],
      732 => [
        'description' => clienttranslate('Pay {1} less for the next Spell you play this turn, down to a minimum of {1}.'),
        'output' =>  [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => SPELL, 'reduction' => 1, 'minimum' => 1, 'permanent' => false]],
        ],
      ],
      733 => [
        'description' => clienttranslate('You may <RUSH> a Character. If played from your hand, it activates one of its {r} abilities.'),
        'output' => FT::SEQ_OPTIONAL(
          FT::ACTION(SPECIAL_EFFECT, [
            'effect' => 'triggerEffectOfNextCharacter',
            'args' => ['type' => CHARACTER, 'from' => HAND, 'limit' => 1, 'effect' => RESERVE],
          ]),
          FT::RUSH_CHARACTER()
        ),
      ],
      734 => [
        'description' => clienttranslate('You may <RUSH>. If you would play a card from Reserve this way, you may pay its Hand Cost instead of its Reserve Cost.'),
        'output' => FT::SEQ_OPTIONAL(
          FT::ACTION(CHOOSE_ASSIGNMENT, ['actions' => ['play'], 'reserveFlipCost' => true, 'mandatory' => true])
        )
      ],
      736 => [
        'description' => clienttranslate('You may discard target Character or Permanent. If its Base Cost was {3} or more, its controller draws a card.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, [
            'minBaseCost' => 3,
            'targetType' => [CHARACTER, PERMANENT],
            'effect' => FT::SEQ(
              FT::ACTION(DISCARD, []),
              FT::ACTION(DRAW, ['players' => 'owner'])
            )
          ]),
          FT::ACTION(TARGET, [
            'maxBaseCost' => 2,
            'targetType' => [CHARACTER, PERMANENT],
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        )
      ],
      737 => [
        'description' => clienttranslate('You may discard target Character. If its Base Cost was {3} or more, its controller draws a card.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, [
            'minBaseCost' => 3,
            'targetType' => [CHARACTER],
            'effect' => FT::SEQ(
              FT::ACTION(DISCARD, []),
              FT::ACTION(DRAW, ['players' => 'owner'])
            )
          ]),
          FT::ACTION(TARGET, [
            'maxBaseCost' => 2,
            'targetType' => [CHARACTER],
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        )
      ],
      739 => [
        'description' => clienttranslate('You may return target Permanent to its owner\'s hand. If you do, create a <MANASEED> token in its controller\'s Landmarks.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::RETURN_TO_HAND(),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'NE_Common_Manaseed',
              'targetLocation' => ['discardedSource'],
              'forcedLocation' => LANDMARK,
              'targetPlayer' => 'owner'
            ]),
          )
        ])
      ],
      756 => [
        'description' => clienttranslate('You may return target Permanent to its owner\'s hand.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'upTo' => true,
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK],
          'effect' => FT::RETURN_TO_HAND()
        ])
      ],
      740 => [
        'description' => clienttranslate('You may exhaust target card in any player\'s Reserve. If you do, create a <MANASEED> token in their Landmarks.'),
        'output' => FT::ACTION(TARGET, [
          'upTo' => true,
          'targetType' => [CHARACTER, PERMANENT, SPELL],
          'targetLocation' => [RESERVE],
          'isNotTapped' => true,
          'effect' => FT::SEQ(
            FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
            FT::ACTION(INVOKE_TOKEN, [
              'targetPlayer' => 'owner',
              'tokenType' => 'NE_Common_Manaseed',
              'targetLocation' => [LANDMARK],
            ]),
          )
        ])
      ],
      741 => [
        'description' => clienttranslate('You may exhaust target card in Reserve. If it\'s in your Reserve, <RESUPPLY_LOW>.'),
        'output' => FT::ACTION(
          TARGET,
          [
            'targetLocation' => [RESERVE],
            'targetType' => [SPELL, CHARACTER, PERMANENT],
            'isNotTapped' => true,
            'upTo' => true,
            'effect' => FT::SEQ(
              FT::ACTION(EXHAUST, []),
              FT::ACTION(CHECK_CONDITION, ['condition' => 'isTargetSameOwner', 'effect' => FT::ACTION(RESUPPLY, [])])
            )
          ]
        )
      ],
      742 => [
        'description' => clienttranslate('You may exhaust target Permanent or target card in Reserve.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'upTo' => true,
            'effect' => FT::ACTION(EXHAUST, ['cardId' => EFFECT])
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT, CHARACTER, SPELL],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::ACTION(EXHAUST, ['cardId' => EFFECT])
          ])
        )
      ],
      743 => [
        'description' => clienttranslate('You may exhaust target Permanent, then you may exhaust target card in Reserve.'),
        'output' => FT::SEQ(
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'upTo' => true,
            'effect' => FT::ACTION(EXHAUST, ['cardId' => EFFECT])
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT, CHARACTER, SPELL],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::ACTION(EXHAUST, ['cardId' => EFFECT])
          ])
        )
      ],
      744 => [
        'description' => clienttranslate('You may target a Character other than me with Base Cost {3} or less, it gains <ANCHORED>.'),
        'output' =>  FT::ACTION(TARGET, [
          'upTo' => true,
          'excludeSelf' => true,
          'maxBaseCost' => 3,
          'effect' => FT::GAIN(EFFECT, ANCHORED)
        ])
      ],
      745 => [
        'description' => clienttranslate('You may target a Character other than me with Base Cost {3} or less, it gains <ANCHORED>.'),
        'output' =>  FT::ACTION(TARGET, [
          'upTo' => true,
          'excludeSelf' => true,
          'maxBaseCost' => 3,
          'effect' => FT::GAIN(EFFECT, ANCHORED)
        ])
      ],
      746 => [
        'description' => clienttranslate('You may target a Character, it gains <ASLEEP>. If you control it, create a <MANASEED> token in your Landmarks.'),
        'output' =>  FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::GAIN(EFFECT, ASLEEP),
            FT::ACTION(CHECK_CONDITION, [
              'condition' => 'hasSameOwner',
              'effect' =>
              FT::ACTION(INVOKE_TOKEN, [
                'pId' => 'source',
                'tokenType' => 'NE_Common_Manaseed',
                'targetLocation' => ['discardedSource'],
                'forcedLocation' => LANDMARK,
                'targetPlayer' => 'owner'
              ]),
            ])
          )
        ]),
      ],
      747 => [
        'description' => clienttranslate('You may target a Character, it gains <ASLEEP>. If you do, create a <MANASEED> token in its controller\'s Landmarks.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::GAIN(EFFECT, ASLEEP),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'NE_Common_Manaseed',
              'targetLocation' => ['discardedSource'],
              'forcedLocation' => LANDMARK,
              'targetPlayer' => 'owner'
            ]),
          )
        ]),
      ],
      752 => [
        'description' => clienttranslate('Play me for {1} less per other card you played this turn.  {J} Pass.'),
        'noTrigger' => true,
        'attributes' => ['dynamicCostReduction' => 'playedCards']
        // hard  code
      ],
      753 => [
        'description' => clienttranslate('Play me for {2} less if you played another card this turn.  {J} Pass.'),
        'noTrigger' => true,
        'attributes' => ['dynamicCostReduction' => 'hasPlayedCards']
        // hard code
      ],
      755 => [
        'description' => clienttranslate('You may ready target Permanent or target card in Reserve.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'upTo' => true,
            'effect' => FT::ACTION(READY, ['cardId' => EFFECT])
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT, CHARACTER, SPELL],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::ACTION(READY, ['cardId' => EFFECT])
          ])
        )
      ],
      790 => [
        'description' => clienttranslate('You may ready target Permanent or target card in Reserve.'),
        'output' => FT::XOR(
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT],
            'upTo' => true,
            'effect' => FT::ACTION(READY, ['cardId' => EFFECT])
          ]),
          FT::ACTION(TARGET, [
            'targetType' => [PERMANENT, CHARACTER, SPELL],
            'targetLocation' => [RESERVE],
            'upTo' => true,
            'effect' => FT::ACTION(READY, ['cardId' => EFFECT])
          ])
        )
      ],
      791 => [
        'description' => clienttranslate('Ready up to two targets, Permanents or cards in Reserve.'),
        'output' => [
          'type' => NODE_OR,
          'args' => ['n' => 2, 'canReuse' => true],
          // 'pId' => 'source',
          'childs' => [
            FT::ACTION(TARGET, [
              'targetType' => [PERMANENT],
              'upTo' => true,
              'effect' => FT::ACTION(READY, ['cardId' => EFFECT])
            ]),
            FT::ACTION(TARGET, [
              'targetType' => [PERMANENT, CHARACTER, SPELL],
              'targetLocation' => [RESERVE],
              'upTo' => true,
              'effect' => FT::ACTION(READY, ['cardId' => EFFECT])
            ])
          ]
        ]
      ],
      738 => ['description' => clienttranslate('Create a <MANASEED> token in target player\'s Landmarks.'), 'output' =>  [
        'action' => INVOKE_TOKEN,
        'args' => ['tokenType' => 'NE_Common_Manaseed', 'targetLocation' => [LANDMARK], 'allPlayers' => true],
      ],],
      691 => ['description' => clienttranslate('<SABOTAGE_LOW>, otherwise I gain 1 boost.'), 'output' => FT::SABOTAGE(), 'oppositeOutput' => FT::GAIN(ME, BOOST)],
      717 => [
        'description' => clienttranslate('Target opponent reveals the top four cards of their deck. You may play a Character from these cards for free and it gains <FLEETING>, then discard the rest.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'RomanticEncounter', 'args' => ['player' => OPPONENT]]),
      ],
      720 => [
        'description' => clienttranslate('Take control of target Permanent with Base Cost {1} or less. If it\'s an Expedition Permanent, it joins my Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => IN_PLAY,
          'targetType' => [PERMANENT],
          'maxBaseCost' => 1,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect', 'args' => ['moveToMe' => true]])
        ])
      ],
      721 => [
        'description' => clienttranslate('Take control of target Permanent with Base Cost {2} or less. If it\'s an Expedition Permanent, it joins my Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => IN_PLAY,
          'targetType' => [PERMANENT],
          'maxBaseCost' => 2,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect', 'args' => ['moveToMe' => true, 'takeControl' => true]])
        ])
      ],
      722 => [
        'description' => clienttranslate('Take control of target token Permanent. If it\'s an Expedition Permanent, it joins my Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetLocation' => IN_PLAY,
          'targetType' => [PERMANENT],
          'onlyToken' => true,
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'defect', 'args' => ['moveToMe' => true, 'takeControl' => true]])
        ])
      ],
      750 => [
        'description' => clienttranslate('Pay {4} less for the next Spell you play this Afternoon.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => SPELL, 'reduction' => 4, 'permanent' => true]],
        ]
      ],
      751 => [
        'description' => clienttranslate('Pay {7} less for the next Spell you play this Afternoon.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => SPELL, 'reduction' => 7, 'permanent' => true]],
        ]
      ],
      748 => [
        'description' => clienttranslate('Pay {4} less for the next Permanent you play this Afternoon.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 4, 'permanent' => true]],
        ]
      ],
      749 => [
        'description' => clienttranslate('Pay {7} less for the next Permanent you play this Afternoon.'),
        'output' => [
          'action' => SPECIAL_EFFECT,
          'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 4, 'permanent' => true]],
        ]
      ],
      697 => [
        'description' => clienttranslate('Distribute 2 boosts among any target Characters in play.'),
        'output' => FT::SEQ(
          FT::ACTION(TARGET, ['targetLocation' => [STORM_LEFT, STORM_RIGHT], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
          FT::ACTION(TARGET, ['targetLocation' => [STORM_LEFT, STORM_RIGHT], 'effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
        ),
      ],
      719 => [
        'description' => clienttranslate('Send to Reserve target Character with Base Cost {4} or less, then exhaust it.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'maxBaseCost' => 4,
          'effect' => FT::SEQ(
            FT::DISCARD_TO_RESERVE(),
            FT::ACTION(EXHAUST, [])
          )
        ]),
      ],
      727 => [
        'description' => clienttranslate('Target player sacrifices the Character or Permanent they control with the highest Base Cost.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'sacrificeHighestCharacterPermanent']),
      ],
      735 => [
        'description' => clienttranslate('You may choose both, otherwise choose one:  • <SABOTAGE>.  • Create a <MANASEED> token in target player\'s Landmarks.'),
        'output' => FT::OR(
          FT::SABOTAGE(),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Manaseed',
            'targetLocation' => [LANDMARK],
            'allPlayers' => true,
          ]),
        ),
        'oppositeOutput' => FT::XOR(
          FT::SABOTAGE(),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'NE_Common_Manaseed',
            'targetLocation' => [LANDMARK],
            'allPlayers' => true,
          ]),
        )
      ], // To see, hard code
      712 => ['description' => clienttranslate('I gain 1 boost and lose <FLEETING>.'), 'output' => FT::SEQ(FT::GAIN(ME, BOOST), FT::LOOSE(ME, FLEETING)),],
      711 => ['description' => clienttranslate('I gain <ANCHORED>.'), 'output' => FT::GAIN(ME, ANCHORED)],
      723 => ['description' => clienttranslate('Target Character gains <ANCHORED>.'), 'output' => FT::ACTION(TARGET, ['effect' => FT::GAIN(EFFECT, ANCHORED)]),],
      754 => [
        'description' => clienttranslate('You may play me for {2} less in an Expedition facing two or more Characters.  {J} Pass.'),
        'noTrigger' => true,
        'attributes' => ['playLimitation' => '-2Multi']
      ],
      705 => [
        'description' => clienttranslate('I activate its {r} abilities as if they were mine.'),
        'output' => FT::ACTION(ACTIVATE_EFFECT, ['effectType' => 'Reserve', 'ownEffect' => true]),
      ],
      699 => [
        'description' => clienttranslate('Draw a card, otherwise create a <MANASEED> token in your Landmarks.'),
        'output' => FT::ACTION(DRAW, ['players' => ME]),
        'oppositeOutput' =>  FT::ACTION(INVOKE_TOKEN, [
          'pId' => 'source',
          'tokenType' => 'NE_Common_Manaseed',
          'targetLocation' => [LANDMARK],
        ]),
      ],
      777 => [
        'description' => clienttranslate('You may target a Character, it gains 1 boost and <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::GAIN(EFFECT, BOOST),
            FT::GAIN(EFFECT, FLEETING),
          )
        ])
      ],
      778 => [
        'description' => clienttranslate('You may target a Character, it gains 2 boosts and <FLEETING>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::GAIN(EFFECT, BOOST, 2),
            FT::GAIN(EFFECT, FLEETING),
          )
        ])
      ],
      779 => ['description' => clienttranslate('<EXHAUSTED_RESUPPLY>.'), 'output' => FT::ACTION(RESUPPLY, ['exhausted' => true])],
      684 => [
        'description' => clienttranslate('Discard up to one target Character, otherwise send up to one target Character to Reserve.'),
        'output' =>  FT::ACTION(TARGET, [
          'effect' => FT::ACTION(DISCARD, []),
        ]),
        'oppositeOutput' => FT::ACTION(TARGET, ['effect' => FT::DISCARD_TO_RESERVE()])
      ],
      685 => [
        'description' => clienttranslate('You may exchange a card other than me from your Reserve with a card from your hand.'),
        'output' => FT::ACTION(EXCHANGE, ['targetType' => [PERMANENT, SPELL, CHARACTER], 'excludeSelf' => true], ['optional' => true])
      ],
      686 => [
        'description' => clienttranslate('You may exchange a Character other than me from your Reserve with a card from your hand.'),
        'output' => FT::ACTION(EXCHANGE, ['targetType' => [CHARACTER], 'excludeSelf' => true], ['optional' => true])
      ],
      687 => [
        'description' => clienttranslate('You may exchange a Spell from your Reserve with a card from your hand.'),
        'output' => FT::ACTION(EXCHANGE, ['targetType' => [SPELL], 'excludeSelf' => true], ['optional' => true])
      ],
      782 => [
        'description' => clienttranslate('Play me for {1} less if you control an exhausted Permanent.'),
        'noTrigger' => true,
        'attributes' => ['dynamicCostReduction' => ['1:hasControl:permanent:1:false:exhausted']]
      ],
      783 => [
        'description' => clienttranslate('Play me for {1} less if there\'s an exhausted card in your Reserve.'),
        'noTrigger' => true,
        'attributes' => ['dynamicCostReduction' => ['1:hasReserve::::GTE:exhausted']]
      ],
      784 => [
        'description' => clienttranslate('You may discard target Character. If you do, create a <MANA_MOTH> Illusion token in its Expedition.'),
        'output' =>  FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'YZ_Common_ManaMoth',
              'targetLocation' => ['discardedSource'],
            ]),
          )
        ]),
      ],
      785 => [
        'description' => clienttranslate('You may return target Character other than me to its owner\'s hand.'),
        'output' => FT::ACTION(TARGET, ['excludeSelf' => true, 'targetType' => [CHARACTER], 'effect' => FT::RETURN_TO_HAND()]),
      ],
      786 => [
        'description' => clienttranslate('You may discard target Character or Permanent with Base Cost {2} or less.'),
        'output' => FT::ACTION(TARGET, [
          'maxBaseCost' => 2,
          'targetType' => [PERMANENT, CHARACTER],
          'effect' => FT::ACTION(DISCARD, [])
        ])
      ],
      787 => [
        'description' => clienttranslate('You may discard target Permanent with Base Cost {3} or less.'),
        'output' => FT::ACTION(TARGET, [
          'maxBaseCost' => 3,
          'upTo' => true,
          'targetType' => [PERMANENT],
          'effect' => FT::ACTION(DISCARD, [])
        ])
      ],
      788 => [
        'description' => clienttranslate('You may send to Reserve target Character with Base Cost {4} or less.'),
        'output' => FT::ACTION(TARGET, [
          'maxBaseCost' => 4,
          'upTo' => true,
          'targetType' => [CHARACTER],
          'effect' => FT::DISCARD_TO_RESERVE()
        ])
      ],
      789 => [
        'description' => clienttranslate('You may discard target Character with Base Cost {3} or less.'),
        'output' => FT::ACTION(TARGET, [
          'maxBaseCost' => 3,
          'upTo' => true,
          'targetType' => [CHARACTER],
          'effect' => FT::ACTION(DISCARD, [])
        ])
      ],
      792 => [
        'description' => clienttranslate('I am <GIGANTIC> and <DEFENDER>.'),
        'noTrigger' => true,
        'attributes' => ['dynamicGigantic' => '1', 'dynamicDefender' => '1'],
      ],
      793 => [
        'description' => clienttranslate('You may discard target Character. If you do, create a <WOOLLYBACK> Animal token in its Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'MU_Common_Woollyback',
              'targetLocation' => ['discardedSource'],
            ]),
          )
        ])
      ],
      795 => [
        'description' => clienttranslate('You may have target opponent draw a card. If you do, target Character gains <ANCHORED>.'),
        'output' => FT::SEQ_OPTIONAL_MANUAL(
          FT::ACTION(TARGET_PLAYER, ['opponentsOnly' => true, 'effect' => FT::ACTION(DRAW, ['players' => ME])]),
          FT::ACTION(TARGET, ['effect' => FT::GAIN(EFFECT, ANCHORED)])
        )
      ],
      914 => [
        'description' => clienttranslate('You may immediately play a Permanent for {2} less.'),
        'output' => FT::SEQ_OPTIONAL(
          [
            'action' => SPECIAL_EFFECT,
            'args' => ['effect' => 'costReduction', 'args' => ['type' => PERMANENT, 'reduction' => 3, 'permanent' => false]],
          ],
          FT::ACTION(CHOOSE_ASSIGNMENT, ['types' => [PERMANENT], 'actions' => ['play']])
        ),
      ],
      103 => [
        'description' => clienttranslate('You may discard any number of cards from your Reserve to draw that many cards.'),
        'output' => FT::ACTION(DISCARD_DO, ['effect' => FT::ACTION(DRAW, ['players' => ME, 'n' => 'X'])])
      ],
       // EOLE outputs
       796 => [
        'description' => clienttranslate('<SABOTAGE> any number of Characters with total {V} less than or equal to my {V} stat.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => STORMS,
          'n' => INFTY,
          'upTo' => true,
          'compareTargetBiome' => ['biome' => FOREST, 'op' => 'lte', 'source' => 'source'],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      799 => [
        'description' => clienttranslate('I am <GIGANTIC>.  When you discard a card from your hand or your Reserve — I gain 1 boost.'),
        'noTrigger' => true,
        'attributes' => ['dynamicGigantic' => '1'],
        'passive' => [
          'Discard' => [
            'childs' => [
              [
                'conditions' => ['isDiscardByOwner', 'isDiscarded:hand:discard'],
                'output' => FT::GAIN(ME, BOOST),
              ],
              [
                'conditions' => ['isDiscardByOwner', 'isDiscarded:reserve:discard'],
                'output' => FT::GAIN(ME, BOOST),
              ],
            ],
          ],
        ],
      ],
      811 => [
        'description' => clienttranslate('<RESUPPLY>. If you put a Character in Reserve this way, it gains 1 boost.'),
        'output' => FT::ACTION(RESUPPLY, ['boostIfMatchCondition' => '1:isType:character']),
      ],
      812 => [
        'description' => clienttranslate('Reveal the top card of your deck. If it\'s a Character, draw it; if not, <RESUPPLY_LOW> it.'),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'revealTop']),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'drawRevealed', 'args' => ['type' => CHARACTER, 'reserveIfNotType' => true]])
        ),
      ],
      813 => [
        'description' => clienttranslate('<SABOTAGE_LOW>, otherwise <SABOTAGE_LOW> a card with Reserve Cost {2} or less.'),
        'output' => FT::SABOTAGE(),
        'oppositeOutput' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'maxReserveCost' => 2,
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      814 => [
        'description' => clienttranslate('<SABOTAGE> a Character with {V} less than or equal to mine.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'compareTargetBiome' => ['biome' => FOREST, 'op' => 'lte', 'source' => 'source'],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      815 => [
        'description' => clienttranslate('<SABOTAGE> a Character with {V} less than or equal to mine.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => [RESERVE],
          'compareTargetBiome' => ['biome' => FOREST, 'op' => 'lte', 'source' => 'source'],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      816 => [
        'description' => clienttranslate('<SABOTAGE>. If you discarded a card this way, create an <ORDIS_RECRUIT> Soldier token in target Expedition controlled by an opponent.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => CONTROLLER,
              'tokenType' => 'OD_Common_OrdisRecruit',
              'targetPlayer' => OPPONENT,
              'targetLocation' => STORMS,
            ])
          ),
        ]),
      ],
      817 => [
        'description' => clienttranslate('<SABOTAGE>. If you discarded a card this way, create a <WOOLLYBACK> Animal token in target Expedition controlled by an opponent.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => CONTROLLER,
              'tokenType' => 'MU_Common_Woollyback',
              'targetPlayer' => OPPONENT,
              'targetLocation' => STORMS,
            ])
          ),
        ]),
      ],
      818 => [
        'description' => clienttranslate('I am <TOUGH_1>.  When any player passes — You may play a card.'),
        'noTrigger' => true,
        'attributes' => ['dynamicTough' => '1'],
        'passive' => [
          'EndTurn' => [
            'output' => FT::ACTION(CHOOSE_ASSIGNMENT, ['actions' => ['play']], ['optional' => true]),
          ],
        ],
      ],
      819 => [
        'description' => clienttranslate('Create a <MANA_MOTH> Illusion token in target Expedition, otherwise I gain 1 boost.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'YZ_Common_ManaMoth',
          'targetLocation' => STORMS,
        ]),
        'oppositeOutput' => FT::GAIN(ME, BOOST),
      ],
      820 => [
        'description' => clienttranslate('Create a <WOOLLYBACK> Animal token in my Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'MU_Common_Woollyback',
          'targetLocation' => ['source'],
        ]),
      ],
      821 => [
        'description' => clienttranslate('Create a <WOOLLYBACK> Animal token in target Expedition, it gains <ANCHORED>.'),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextTokenAnchored']),
          FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'MU_Common_Woollyback',
          ]),
        ),
      ],
      822 => [
        'description' => clienttranslate('Create a <WOOLLYBACK> Animal token in the Expedition facing me.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'targetPlayer' => OPPONENT,
          'targetLocation' => ['source'],
          'tokenType' => 'MU_Common_Woollyback',
        ]),
      ],
      823 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in each of your <ASCENDED_P> Expeditions.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'invokeRecruitOnAscendedExpeditions']),
      ],
      824 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in my Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => ['source'],
        ]),
      ],
      825 => [
        'description' => clienttranslate('Create an <ORDIS_RECRUIT> Soldier token in target Expedition I\'m not in.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'OD_Common_OrdisRecruit',
          'targetLocation' => ['oppositeSource'],
        ]),
      ],
      826 => [
        'description' => clienttranslate('Discard a card from your hand.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      827 => [
        'description' => clienttranslate('Discard it.'),
        'output' => FT::ACTION(DISCARD, ['cardId' => EFFECT]),
      ],
      828 => [
        'description' => clienttranslate('Discard two cards from your hand.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'n' => 2,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      830 => [
        'description' => clienttranslate('Draw one card per <COMPLETED_LOW> Feat you control.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'drawXCompletedFeat']),
      ],
      831 => [
        'description' => clienttranslate('Draw a card, otherwise create an <AEROLITH> token in your Landmarks.'),
        'output' => FT::ACTION(DRAW, ['players' => ME]),
        'oppositeOutput' => [
          'action' => INVOKE_TOKEN,
          'automatic' => true,
          'args' => ['tokenType' => 'NE_Common_Aerolith', 'targetLocation' => [LANDMARK]],
        ],
      ],
      832 => [
        'description' => clienttranslate('Draw a card, then discard a card from your hand.'),
        'output' => FT::SEQ(
          FT::ACTION(DRAW, ['players' => ME]),
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'effect' => FT::ACTION(DISCARD, []),
          ])
        ),
      ],
      833 => [
        'description' => clienttranslate('Draw three cards, then discard a card from your hand.'),
        'output' => FT::SEQ(
          FT::ACTION(DRAW, ['players' => ME, 'n' => 3]),
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'effect' => FT::ACTION(DISCARD, []),
          ])
        ),
      ],
      835 => [
        'description' => clienttranslate('Draw two cards, then discard a card from your hand.'),
        'output' => FT::SEQ(
          FT::ACTION(DRAW, ['players' => ME, 'n' => 2]),
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'effect' => FT::ACTION(DISCARD, []),
          ])
        ),
      ],
      836 => [
        'description' => clienttranslate('Draw two cards.'),
        'output' => FT::ACTION(DRAW, ['players' => ME, 'n' => 2]),
      ],
      837 => [
        'description' => clienttranslate('Each of your Expeditions <ASCENDS>.'),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => STORM_LEFT]),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => STORM_RIGHT])
        ),
      ],
      838 => [
        'description' => clienttranslate('Each player discards their Reserve, then <RESUPPLIES>.'),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'discardAllReserve']),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerResupply'])
        ),
      ],
      839 => [
        'description' => clienttranslate('Exchange its boosts with target Character in play or in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, RESERVE],
          'excludeSelf' => true,
          'excludePreviousTarget' => true,
          'effect' => FT::ACTION(BOOST_EXCHANGE, []),
        ]),
      ],
      840 => [
        'description' => clienttranslate('You may have me activate my {D} ability.'),
        'output' => FT::ACTION(ACTIVATE_EFFECT, ['effectType' => 'Support', 'ownEffect' => true], ['optional' => true]),
      ],
      841 => [
        'description' => clienttranslate('Each of your Expeditions <ASCENDS>, otherwise my Expedition <ASCENDS>.'),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => STORM_LEFT]),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => STORM_RIGHT])
        ),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'source']),
      ],
      843 => [
        'description' => clienttranslate('I gain 1 boost per <ASCENDED_S> Expedition you control.'),
        'output' => FT::ACTION(CHECK_CONDITION, [
          'condition' => 'hasSourcePlayerAllAscended',
          'effect' => FT::GAIN(ME, BOOST, 2),
          'oppositeEffect' => FT::ACTION(CHECK_CONDITION, [
            'condition' => 'hasSourcePlayerAscended',
            'effect' => FT::GAIN(ME, BOOST),
          ]),
        ]),
      ],
      844 => [
        'description' => clienttranslate('I gain 1 boost per Animal other than me in your Expeditions.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXAnimalsInExpeditions']),
      ],
      845 => [
        'description' => clienttranslate('I gain 1 boost per Feat in your Landmarks.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXFeat']),
      ],
      846 => [
        'description' => clienttranslate('I gain 1 boost, otherwise discard the top card of your deck.'),
        'output' => FT::GAIN(ME, BOOST),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'discardTopDeck', 'args' => ['n' => 1]]),
      ],
      847 => [
        'description' => clienttranslate('I gain 1 boost, otherwise discard the top two cards of your deck.'),
        'output' => FT::GAIN(ME, BOOST),
        'oppositeOutput' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'discardTopDeck', 'args' => ['n' => 2]]),
      ],
      848 => [
        'description' => clienttranslate('I gain 1 boost.'),
        'output' => FT::GAIN(ME, BOOST),
      ],
      849 => [
        'description' => clienttranslate('It activates one of its {j} abilities.'),
        'output' => FT::ACTION(ACTIVATE_EFFECT, ['cardId' => EFFECT, 'n' => 1]),
      ],
      850 => [
        'description' => clienttranslate('It gains <ANCHORED>.'),
        'output' => FT::GAIN(EFFECT, ANCHORED),
      ],
      851 => [
        'description' => clienttranslate('It gains [ASLEEP].'),
        'output' => FT::GAIN(EFFECT, ASLEEP),
      ],
      852 => [
        'description' => clienttranslate('It gains [FLEETING], <ANCHORED> or [ASLEEP].'),
        'output' => FT::XOR(
          FT::GAIN(EFFECT, FLEETING),
          FT::GAIN(EFFECT, ANCHORED),
          FT::GAIN(EFFECT, ASLEEP)
        ),
      ],
      853 => [
        'description' => clienttranslate('It gains 2 boosts.'),
        'output' => FT::GAIN(EFFECT, BOOST, 2),
      ],
      854 => [
        'description' => clienttranslate('It gains 1 boost and loses [FLEETING].'),
        'output' => FT::SEQ(FT::GAIN(EFFECT, BOOST), FT::LOOSE(EFFECT, FLEETING)),
      ],
      855 => [
        'description' => clienttranslate('It switches Expeditions.'),
        'output' => FT::ACTION(MOVE_CARD, ['cardId' => EFFECT]),
      ],
      856 => [
        'description' => clienttranslate('You play Permanents for {1} less, down to a minimum of {1}.'),
        'attributes' => ['reduceCostType' => [PERMANENT => ['minBaseCost' => 0, 'reduction' => 1, 'minimum' => 1]]],
      ],
      857 => [
        'description' => clienttranslate('Pay {1} less for the next Permanent you play this Afternoon, down to a minimum of {1}.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'costReduction',
          'args' => ['type' => PERMANENT, 'reduction' => 1, 'minimum' => 1, 'permanent' => true],
        ]),
      ],
      858 => [
        'description' => clienttranslate('Pay {1} less for the next Feat you play this turn, down to a minimum of {1}.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, [
          'effect' => 'costReduction',
          'args' => ['type' => FEAT, 'reduction' => 1, 'minimum' => 1, 'permanent' => false],
        ]),
      ],
      859 => [
        'description' => clienttranslate('Put it into its owner\'s Mana zone (as an exhausted Mana Orb).'),
        'output' => FT::ACTION(DISCARD, ['cardId' => EFFECT, 'destination' => MANA, 'tapped' => true, 'force' => true]),
      ],
      860 => [
        'description' => clienttranslate('Ready a Mana Orb.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [MANA],
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'isTapped' => true,
          'effect' => FT::ACTION(READY, ['cardId' => EFFECT]),
        ]),
      ],
      862 => [
        'description' => clienttranslate('Roll a die. Then, you may send to Reserve target Character with Base Cost {X} or less, where X is the result.'),
        'output' => FT::ACTION(ROLL_DIE, [
          'effect' => [
            '1' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'maxBaseCost' => 1, 'effect' => FT::DISCARD_TO_RESERVE()]),
            '2' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'maxBaseCost' => 2, 'effect' => FT::DISCARD_TO_RESERVE()]),
            '3' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'maxBaseCost' => 3, 'effect' => FT::DISCARD_TO_RESERVE()]),
            '4' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'maxBaseCost' => 4, 'effect' => FT::DISCARD_TO_RESERVE()]),
            '5' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'maxBaseCost' => 5, 'effect' => FT::DISCARD_TO_RESERVE()]),
            '6' => FT::ACTION(TARGET, ['targetType' => [CHARACTER, TOKEN], 'upTo' => true, 'maxBaseCost' => 6, 'effect' => FT::DISCARD_TO_RESERVE()]),
          ],
        ]),
      ],
      863 => [
        'description' => clienttranslate('Send it to Reserve.'),
        'output' => FT::DISCARD_TO_RESERVE(),
      ],
      865 => [
        'description' => clienttranslate('The next Character you play this turn gains <ANCHORED>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterAnchored']),
      ],
      866 => [
        'description' => clienttranslate('The next Character you play this turn gains 1 boost and you may have it gain [ASLEEP].'),
        'output' => FT::SEQ(
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterGains1Boost']),
          FT::ACTION(SPECIAL_EFFECT, ['effect' => 'nextCharacterAsleep', 'optional' => true]),
        ),
      ],
      867 => [
        'description' => clienttranslate('We both gain 1 boost.'),
        'output' => FT::SEQ(FT::GAIN(ME, BOOST), FT::GAIN(EFFECT, BOOST)),
      ],
      868 => [
        'description' => clienttranslate('You may discard a card from your hand to discard target card in play with equal or lower Hand Cost.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'upTo' => true,
          'effect' => FT::ACTION(TARGET, [
            'targetLocation' => STORMS,
            'effect' => FT::ACTION(DISCARD, []),
          ]),
        ]),
      ],
      869 => [
        'description' => clienttranslate('You may discard target Character with Base Cost {1} or less.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'maxBaseCost' => 1,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      870 => [
        'description' => clienttranslate('You may discard target Character with Base Cost {2} or less. If you do, create an <ORDIS_RECRUIT> Soldier token in its Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'maxBaseCost' => 2,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, ['pId' => CONTROLLER, 'tokenType' => 'OD_Common_OrdisRecruit', 'targetLocation' => ['discardedSource']])
          ),
        ]),
      ],
      871 => [
        'description' => clienttranslate('You may discard target Character with Base Cost {2} or less. If you do, create a <WOOLLYBACK> Animal token in its Expedition.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'maxBaseCost' => 2,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, ['pId' => CONTROLLER, 'tokenType' => 'MU_Common_Woollyback', 'targetLocation' => ['discardedSource']])
          ),
        ]),
      ],
      872 => [
        'description' => clienttranslate('You may discard target non-Companion token.'),
        'output' => FT::ACTION(TARGET, [
          'onlyToken' => true,
          'targetType' => [CHARACTER, PERMANENT],
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK],
          'notSubTypes' => [COMPANION],
          'upTo' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      873 => [
        'description' => clienttranslate('You may return target Character facing me with Base Cost {2} or less to its owner\'s hand.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => ['opponentSource'],
          'upTo' => true,
          'maxBaseCost' => 2,
          'effect' => FT::RETURN_TO_HAND(),
        ]),
      ],
      874 => [
        'description' => clienttranslate('You may return target Character facing me with Base Cost {3} or less to its owner\'s hand.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => ['opponentSource'],
          'upTo' => true,
          'maxBaseCost' => 3,
          'effect' => FT::RETURN_TO_HAND(),
        ]),
      ],
      875 => [
        'description' => clienttranslate('You may send to Reserve target Character with Base Cost {1} or less.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'maxBaseCost' => 1,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      876 => [
        'description' => clienttranslate('You may send to Reserve target Character with Base Cost {2} or less.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'upTo' => true,
          'maxBaseCost' => 2,
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      877 => [
        'description' => clienttranslate('You may target a Character in play or in Reserve with a {D} ability, it gains 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => [STORM_LEFT, STORM_RIGHT, RESERVE],
          'upTo' => true,
          'hasEffects' => ['Support'],
          'effect' => FT::GAIN(EFFECT, BOOST, 2),
        ]),
      ],
      878 => [
        'description' => clienttranslate('You may target a Character in Reserve, it gains 2 boosts.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::GAIN(EFFECT, BOOST, 2),
        ]),
      ],
      880 => [
        'description' => clienttranslate('You may target a Character with Base Cost {3} or less other than me, it activates one of its {j} abilities.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'excludeSelf' => true,
          'upTo' => true,
          'maxBaseCost' => 3,
          'effect' => FT::ACTION(ACTIVATE_EFFECT, ['cardId' => EFFECT, 'n' => 1]),
        ]),
      ],
      881 => [
        'description' => clienttranslate('You may target a Feat you control, it activates its {j} abilities.'),
        'output' =>  FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'targetPlayer' => ME,
          'subType' => FEAT,
          'upTo' => true,
          'hasEffects' => ['Played'],
          'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
        ]),
      ],
      916 => [
        'description' => clienttranslate('Characters facing me are [SEASONED_CHA_P].'),
        'noTrigger' => true,
        'attributes' => ['dynamicSeasoned' => 'oppositeSourceCharacter'],
      ],
      918 => [
        'description' => clienttranslate('Characters you control lose their boosts.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'n' => INFTY,
          'effect' => FT::ACTION(SPEND, ['n' => INFTY]),
        ]),
      ],
      919 => [
        'description' => clienttranslate('Characters you control other than me can\'t gain boosts.'),
        'noTrigger' => true,
        'attributes' => ['cantGainBoost' => 'character:excludeSelf'],
      ],
      920 => [
        'description' => clienttranslate('Create a <WOOLLYBACK> Animal token in each of your Expeditions.'),
        'output' => FT::SEQ(
          FT::ACTION(INVOKE_TOKEN, ['pId' => CONTROLLER, 'tokenType' => 'MU_Common_Woollyback', 'targetLocation' => [STORM_LEFT]]),
          FT::ACTION(INVOKE_TOKEN, ['pId' => CONTROLLER, 'tokenType' => 'MU_Common_Woollyback', 'targetLocation' => [STORM_RIGHT]])
        ),
      ],
      921 => [
        'description' => clienttranslate('Discard a card from your Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [RESERVE],
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      922 => [
        'description' => clienttranslate('Discard all cards from your Reserve other than me.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [RESERVE],
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'n' => INFTY,
          'excludeSelf' => true,
          'effect' => FT::ACTION(DISCARD, []),
        ]),
      ],
      923 => [
        'description' => clienttranslate('Each Character you control gains [FLEETING].'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'n' => INFTY,
          'effect' => FT::GAIN(EFFECT, FLEETING),
        ]),
      ],
      924 => [
        'description' => clienttranslate('Exhaust one of your Mana Orbs.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [MANA],
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'isNotTapped' => true,
          'effect' => FT::ACTION(EXHAUST, ['cardId' => EFFECT]),
        ]),
      ],
      925 => [
        'description' => clienttranslate('Put a card from your hand in Reserve.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'effect' => FT::DISCARD_TO_RESERVE(),
        ]),
      ],
      926 => [
        'description' => clienttranslate('Put a card from your hand on top of your deck.'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'effect' => FT::ACTION(DISCARD, ['destination' => TOP_OF_DECK]),
        ]),
      ],
      927 => [
        'description' => clienttranslate('Target Character facing me gains <ANCHORED>.'),
        'output' => FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, TOKEN],
          'targetLocation' => ['oppositeSource'],
          'effect' => FT::GAIN(EFFECT, ANCHORED),
        ]),
      ],
      928 => [
        'description' => clienttranslate('Target Character you control other than me gains [ASLEEP].'),
        'output' => FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'excludeSelf' => true,
          'effect' => FT::GAIN(EFFECT, ASLEEP),
        ]),
      ],
      929 => [
        'description' => clienttranslate('Target opponent may have me switch Expeditions.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => true,
          'effect' => FT::ACTION(MOVE_CARD, ['cardId' => ME], ['optional' => true, 'pId' => 'active']),
        ]),
      ],
      930 => [
        'description' => clienttranslate('Target opponent may put a card from their Reserve in their Mana zone (as an exhausted Mana Orb).'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => true,
          'effect' => FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetLocation' => [RESERVE],
            'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
            'upTo' => true,
            'effect' => FT::ACTION(DISCARD, ['destination' => MANA, 'tapped' => true]),
          ], ['pId' => 'active']),
        ]),
      ],
      931 => [
        'description' => clienttranslate('Target opponent may ready a Mana Orb.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => true,
          'effect' => FT::SEQ_OPTIONAL(FT::ACTION(READY, ['cardId' => MANA], ['pId' => 'active'])),
        ]),
      ],
      932 => [
        'description' => clienttranslate('Target opponent may return a card from their Reserve to their hand.'),
        'output' => FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => true,
          'effect' => FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetLocation' => [RESERVE],
            'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
            'upTo' => true,
            'effect' => FT::RETURN_TO_HAND(),
          ], ['pId' => 'active']),
        ]),
      ],
      933 => [
        'description' => clienttranslate('The Expedition facing me <ASCENDS>.'),
        'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'ascend', 'expedition' => 'oppositeSource']),
      ],
      934 => [
        'description' => clienttranslate('Characters in your other Expedition (the one I\'m not in) are [DEFENDER_CHA_P].'),
        'noTrigger' => true,
        'attributes' => ['dynamicDefender' => 'otherExpeditionCharacter'],
      ],
      936 => [
        'description' => clienttranslate('Create an <AEROLITH> token in target opponent\'s Landmarks.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'NE_Common_Aerolith',
          'targetPlayer' => OPPONENT,
          'targetLocation' => [LANDMARK],
        ]),
      ],
      938 => [
        'description' => clienttranslate('Create a <WOOLLYBACK> Animal token in target Expedition.'),
        'output' => FT::ACTION(INVOKE_TOKEN, [
          'pId' => CONTROLLER,
          'tokenType' => 'MU_Common_Woollyback',
          'targetLocation' => STORMS,
        ]),
      ],
      939 => [
        'description' => clienttranslate('If my Expedition would move forward <DUE_TO_ASCENSION>, it moves forward one more region instead.'),
        'noTrigger' => true,
        'attributes' => ['actionInsteadAdvance' => 'PegasusCommon'],
      ],
      940 => [
        'description' => clienttranslate('I gain 2 boosts.'),
        'output' => FT::GAIN(ME, BOOST, 2),
      ],
    ];
  }

  public static function constructEffect($trinity, &$properties)
  {
    $calculated = [];

    if (isset($trinity['trigger'])) {
      self::computeTrigger($trinity['trigger'], $calculated);
    }
    if (isset($trinity['condition'])) {
      self::computeConditions($trinity['condition'], $calculated);
    }
    if (isset($trinity['output'])) {
      self::computeOutput($trinity['output'], $calculated);
    }

    // throw new \feException(print_r($calculated));

    if (!isset($calculated['type'])) {
      var_dump($trinity);
      throw new \feException(print_r($properties));
    }

    $key = $calculated['type'];
    $node = [];
    if (isset($calculated['noTrigger']) && $calculated['noTrigger'] === true) {
      // DynamicAttributes wll be used
      // if it exists, condition must be added at the end of the dynamic attribute
      $unlessConditions = $calculated['conditionConditions'] ?? [];
      $dynamicAttributeConditions = self::getDynamicAttributeConditionList($trinity, $calculated, $unlessConditions);
      $dynamicAttributeConditionStr = !empty($dynamicAttributeConditions)
        ? ':' . implode('|', $dynamicAttributeConditions)
        : '';

      // if the no trigger is linked to infinite reserve effect
      // throw new \feException(print_r($calculated));
      if (isset($calculated['type']) && $calculated['type'] == 'effectInfinity') {
        $tmp = $calculated['outputAttributes'];
        unset($calculated['outputAttributes']);
        $calculated['outputAttributes']['effectInfinity'] = $tmp;
      }

      if (isset($calculated['conditionConditions'])) {
        $calculated['triggerConditions'] = array_merge(
          $calculated['triggerConditions'] ?? [],
          $calculated['conditionConditions']
        );
        $calculated['triggerDescription'] = array_merge([$calculated['triggerDescription']] ?? [], [
          $calculated['conditionDescription'],
        ]);
        unset($calculated['conditionConditions']);
        unset($calculated['conditionDescription']);
      }
      // throw new \feException(print_r($calculated));
      if (isset($calculated['triggerConditions'])) {
        foreach ($calculated['outputAttributes'] ?? [] as $keyAttribute => $attribute) {
          if ($keyAttribute == 'excludeUniversalTough') {
            $properties[$keyAttribute] = $attribute;
            continue;
          }
          if (!isset($properties[$keyAttribute])) {
            if (is_string($attribute) || is_bool($attribute)) {
              $properties[$keyAttribute] = $attribute . $dynamicAttributeConditionStr;
            }
          } else {
            if (!is_array($properties[$keyAttribute])) {
              $tmp = [$properties[$keyAttribute]];
            }
            if (is_string($attribute) || is_bool($attribute)) {
              $tmp[] = $attribute . $dynamicAttributeConditionStr;
            }
            $properties[$keyAttribute] = $tmp;
          }
        }
      } else {
        // no condition on the trigger
        foreach ($calculated['outputAttributes'] ?? [] as $keyAttribute => $attribute) {
          if (!isset($properties[$keyAttribute])) {
            $properties[$keyAttribute] = $attribute;
          } else {
            if (!is_array($properties[$keyAttribute])) {
              $tmp = [$properties[$keyAttribute]];
            }
            if (is_string($attribute) || is_bool($attribute)) {
              $tmp[] = $attribute;
            }
            $properties[$keyAttribute] = $tmp;
          }
        }
        // throw new \feException(print_r($properties));
      }
    } elseif ($key == 'manInTheMazeUnique') {
      $toAdd[$calculated['triggerConditions'][0]] = $calculated['output'];
      if ($calculated['triggerConditions'][0] == '4+') {
        $properties = Utils::updateTree($properties, 'OUTPUT4', $calculated['output']);
      } elseif ($calculated['triggerConditions'][0] == '9+') {
        $properties = Utils::updateTree($properties, 'OUTPUT9', $calculated['output']);
      }
    } elseif ($key != 'effectPassive' && $key != 'effectInfinity') {
      // no natural condition check, we need to insert CheckConditions
      if (isset($calculated['triggerConditions'])) {
        self::insertCheckCondition($calculated['triggerConditions'], $node, [
          $calculated['conditionDescription'] ?? null,
          $calculated['outputDescription'] ?? null,
        ]);
      }
      if (isset($calculated['conditionEffect'])) {
        self::addEffectToCondition($calculated['conditionEffect'], $node);
      }
    } else {
      if (isset($calculated['trigger'])) {
        if (!is_array($calculated['trigger'])) {
          $calculated['trigger'] = [$calculated['trigger']];
        }

        $template = [];
        if (isset($calculated['triggerConditions'])) {
          $template['conditions'] = $calculated['triggerConditions'];
        }
        if (isset($calculated['triggerListeningConditions'])) {
          $template['listeningConditions'] = $calculated['triggerListeningConditions'];
        }

        if (isset($calculated['oppositeOutput'])) {
          // if it's a "if you do " with otherwise, it cannot be a check condition as an action from the user is expected
          if ($calculated['ifYouDo']) {
            // put a XOR node
            $template['output'] = FT::XOR(
              $calculated['conditionEffect'] ?? 'OUTPUT',
              $calculated['oppositeOutput']
            );
          } else {
            // management of Otherwise effect
            // Need to add another check condition as the trigger condition must be valid, but the condition is with the opposite
            $template['output'] = FT::ACTION(CHECK_CONDITION, [
              'conditions' => $calculated['conditionConditions'] ?? [],
              'effect' => $calculated['conditionEffect'] ?? 'OUTPUT',
              'oppositeEffect' => $calculated['oppositeOutput'],
              'description' => [$calculated['conditionDescription'] ?? null, $calculated['outputDescription'] ?? null],
            ]);
          }
        } else {
          if (isset($calculated['conditionEffect'])) {
            $template['output'] = $calculated['conditionEffect'];
          } else {
            $template['output'] = 'OUTPUT';
          }
          // If there is a condition on condition, we add it
          if (isset($calculated['conditionConditions'])) {
            $template['conditions'] = array_merge($template['conditions'] ?? [], $calculated['conditionConditions']);
          }
          // if (isset($calculated['oppositeOutput'])) {
          //   $template['oppositeOutput'] = $calculated['oppositeOutput'];
          // } else {
          //   $template['oppositeOutput'] = 'OPPOSITE';
          // }
        }
        if (isset($calculated['conditionN'])) {
          $template['n'] = $calculated['conditionN'];
        }

        // Management of AfterRest effects (must happen after cleanup but the cards have been cleanup)
        if (($calculated['afterRest'] ?? '') == true) {
          $newOutput = ['action' => SPECIAL_EFFECT, 'args' => ['effect' => 'afterRest', 'args' => $calculated['output']]];
          $calculated['output'] = $newOutput;
        }

        foreach ($calculated['trigger'] as $t => $trig) {
          // add conditions + effect + output
          $node[$trig] = $template;
        }
      }
    }
    // throw new \feException(print_r($node));
    // output

    if (in_array($trinity['condition'] ?? null, [642, 643]) && isset($calculated['output'])) {
      $calculated['output'] = Utils::tagTree($calculated['output'], ['pId' => 'owner']);
    }

    if (isset($calculated['output'])) {
      self::addOutputToNode($calculated['output'], $node);
    }
    if (isset($calculated['oppositeOutput'])) {
      // if "opposite" is already defined we update it
      if (Utils::searchTree($node, 'OPPOSITE')) {
        // throw new \feException('titi');
        self::addOppositeToNode($calculated['oppositeOutput'], $node);
      } elseif ($key != 'effectPassive') {
        // we nest the actual node in a XOR
        // throw new \feException(print_r($node));
        $node = FT::XOR($node, $calculated['oppositeOutput']);
      }
    }

    // Specific interaction
    if (is_array($node)) {
      foreach ($node as $tr => &$eff) {
        // specific case for "When an opponent draws one or more cards or does Resupply "
        if ($tr == 'Morning') {
          if ($eff['conditions'] == ['isOpponentDraw', 'realResupply']) {
            $eff['conditions'] = ['isMe'];
          }
          if ($eff['conditions'] == ['isOpponentDraw']) {
            $eff['conditions'] = ['isMe'];
          }
        }
        // Management of defect
        if (isset($calculated['pId'])) {
          $eff['pId'] = $calculated['pId'];
        }
      }
    }

    if ($calculated['ifYouDo'] && isset($calculated['oppositeOutput'])) {
      // we remove the optional values
      $node = Utils::updateTree($node, true, false, ['optional']);
      $node = Utils::updateTree($node, true, false, ['upTo']);
    }

    // needed for passive effects in reaction to a classical output (171 for example)
    if (isset($calculated['passiveEffect'])) {
      if (isset($calculated['output'])) {
        self::addOutputToNode($calculated['output'], $calculated['passiveEffect']);
      }

      if (isset($properties['effectPassive'])) {
        // /////////////
        // foreach ($properties['effectPassive'] as $existingTrigger => &$existingPassive) {
        //   // Nothing to merge as it doesn't exist
        //   if (!isset($calculated['passiveEffect'][$existingTrigger])) {
        //     continue;
        //   }
        //   throw new \feException("titi");
        //   // we already have childs
        //   if (isset($existingPassive['childs'])) {
        //     $existingPassive['childs'][] = $node[$existingTrigger];
        //   } else {
        //     $existingPassive = ['childs' => array_merge([$existingPassive], [$calculated['passiveEffect'][$existingTrigger]])];
        //   }
        //   unset($calculated['passiveEffect'][$existingTrigger]);
        // }

        ////////////////////
        $properties['effectPassive'] = array_merge($properties['effectPassive'], $calculated['passiveEffect']);
      } else {
        $properties['effectPassive'] = $calculated['passiveEffect'];
      }
    }

    if ((!isset($calculated['noTrigger']) || $calculated['noTrigger'] === false) && isset($calculated['outputAttributes'])) {
      $properties = array_merge($properties, $calculated['outputAttributes']);
    }

    // edge cases:
    if (in_array($trinity['trigger'], [8, 231]) && $trinity['condition'] == 190 && $trinity['output'] == 44) {
      $properties['sacrificeAndNotFleetingGoToReserve'] = true;
      $node = [];
    } elseif ($trinity['trigger'] == 239) {
      // Edge case for When an opponent draws one or more
      // bug #140378
      $node['Morning']['conditions'] = ['isMe'];
      $node['Resupply']['conditions'][] = 'realResupply';
    } elseif ($trinity['trigger'] == 250) {
      $node['Resupply']['conditions'] = ['isMe'];
    } elseif ($trinity['output'] == 529) {
      // The hunger
      $properties['effectHand'] = FT::ACTION(SPECIAL_EFFECT, ['effect' => 'hunger']);
      $key = 'effectPassive';
      $node = [];
      $node['Discard'] = [
        'conditions' => ['isDiscarded::discard'],
        'output' => FT::GAIN(ME, BOOST),
      ];
      $node['ChooseAssignment'] = [
        'conditions' => ['isAfternoon', 'isFromReserve', 'isSupportEffect'],
        'output' => FT::GAIN(ME, BOOST),
      ];
    } elseif ($trinity['trigger'] == 797 && isset($node['ChooseAssignment']) && !isset($node['Discard'])) {
      $node['Discard'] = $node['ChooseAssignment'];
    } elseif ($trinity['trigger'] == 446) {
      // We need to add the special effect
      $node['LeaveExpedition']['output'] = FT::ACTION(SPECIAL_EFFECT, [
        'effect' => 'doEachBoost',
        'args' => ['effect' => $node['LeaveExpedition']['output']],
      ]);
    } elseif ($trinity['trigger'] == 26 && !in_array($trinity['output'], [56, 97])) {
      // #147483: "Unique lyra - Timing limbo effect/cleanup
      // We add a flag to force listening except for power I gain 1 boost/2 boost
      $node['RollDie']['forceListening'] = true;
    } elseif (in_array($trinity['trigger'], [419, 253])) {
      // #170850: "Amarok and hooked token "
      // needs to manage movecard with Amarok
      $moveCardOutput = $node['InvokeToken']['output'] ?? $node['ChooseAssignment']['output'] ?? null;
      if ($moveCardOutput !== null) {
        $node['MoveCard']['conditions'] = ['isCharacterFromTarget', 'isPlayedInSameLocation', 'excludeSelf'];
        $node['MoveCard']['output'] = $moveCardOutput;
      }
    } elseif ($trinity['output'] == 568) {
      // Zaratan uniques
      $properties['gigantic'] = true;
      $properties['effectPlayed'] = FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXOpponentExpedition']);
    } elseif ($trinity['output'] == 593) {
      // Lucan uniques
      $properties['gigantic'] = true;
      $properties['effectPlayed'] = FT::ACTION(DISCARD_DO, ['effect' => FT::GAIN(ME, BOOST, 'X')]);
    } elseif ($trinity['output'] == 607) {
      // Lucan uniques
      $properties['gigantic'] = true;
      $properties['effectPlayed'] = FT::ACTION(DISCARD_DO, [
        'sacrifice' => true,
        'targetType' => [PERMANENT],
        'targetLocation' => IN_PLAY,
        'effect' => FT::GAIN(ME, BOOST, 'X'),
      ]);
    } elseif ($trinity['output'] == 579) {
      // DoppelGanger
      $properties['effectHand'] = FT::ACTION(TARGET, [
        'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'doppelganger']),
        'targetLocation' => ['opponentSource'],
      ]);
      $properties['effectPassive'] = [
        'LeaveExpedition' => [
          'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'resetCard']),
        ],
      ];
      $properties['seasoned'] = true;
    } elseif ($trinity['output'] == 646) {
      if ($trinity['trigger'] == 22) {
        $properties['effectHand'] = FT::ACTION(TARGET, [
          'upTo' => true,
          'effect' => FT::GAIN(EFFECT, ASLEEP),
        ]);
        $properties['effectPassive']['Gain'] = [
          'conditions' => ['isSource', 'isFacingSource'],
          'output' => FT::SABOTAGE(),
        ];
      } elseif ($trinity['trigger'] == 24) {
        $properties['effectPlayed'] = FT::ACTION(TARGET, [
          'upTo' => true,
          'effect' => FT::GAIN(EFFECT, ASLEEP),
        ]);
        $properties['effectPassive']['Gain'] = [
          'conditions' => ['isSource', 'isFacingSource'],
          'output' => FT::SABOTAGE(),
        ];
      } elseif ($trinity['trigger'] == 1) {
        $properties['effectReserve'] = FT::ACTION(TARGET, [
          'upTo' => true,
          'effect' => FT::GAIN(EFFECT, ASLEEP),
        ]);
        $properties['effectPassive']['Gain'] = [
          'conditions' => ['isSource', 'isFacingSource'],
          'output' => FT::SABOTAGE(),
        ];
      }
    } elseif ($trinity['output'] == 114 && $trinity['condition'] == 357) {
      $properties['costReductionIfEmpty'] = 2;
      unset($properties['dynamicCostReduction']);
    } elseif ($trinity['trigger'] == 258) {
      $node['MoveExpedition']['conditions'][] = 'isNotNight';
      // $node['AfterDusk']['conditions'][] = 'isNight'; // removed as afterdusk is necessarily during night
    } elseif ($trinity['output'] == 752 || $trinity['output'] == 753 || $trinity['output'] == 754) {
      $properties['effectPlayed'] = FT::ACTION(END_AFTERNOON, []);
    } elseif ($trinity['trigger'] == 542) {
      $node['EatMeEnergyBars']['conditions'] = ['isPlayedInOpponentOtherExp'];
      unset($node['EatMeEnergyBars']['listeningConditions']);
    } elseif (in_array($trinity['trigger'], [688, 689])) {
      $node['InvokeToken']['listeningConditions'] = ['isMyTurn', 'isAfternoon', 'isNotMeInvoke', 'notTapped'];
    } elseif ($trinity['trigger'] == 5) {
      $node['InvokeToken']['conditions'][] = 'isMeInvoke';
    }
    // elseif (in_array($trinity['condition'], [642, 643])) {
    //   // we need to force owner after reveal
    //   $node = Utils::tagTree($node, ['pId' => 'owner']);
    // }

    // dynamic attributes generate empty node
    if (!empty($node)) {
      if (isset($properties[$key])) {
        // parallel node for non passive effects
        if (!in_array($key, ['effectPassive', 'effectInfinity'])) {
          // there is already an effect, check if there is an PAR node, to add the node
          if (($properties[$key]['type'] ?? '') == NODE_PARALLEL) {
            $properties[$key]['childs'][] = $node;
          } else {
            // Merge as PARALLEL — type must be set so consumers flatten instead of nesting another PAR
            $oldNode = $properties[$key];
            unset($properties[$key]);
            $properties[$key] = [
              'type' => NODE_PARALLEL,
              'childs' => [$oldNode, $node],
            ];
          }
        } elseif ($key == 'effectInfinity') {
          // We need to merge everything depending on triggers or not.
          // If there are triggers, it must be wrapped in EffectPassive, else as it is.
          if (isset($calculated['trigger'])) {
            if (isset($properties[$key]['effectPassive'])) {
              foreach ($properties[$key]['effectPassive'] as $existingTrigger => &$existingNode) {
                // Nothing to merge as it doesn't exist
                if (!isset($node[$existingTrigger])) {
                  continue;
                }
                // we already have childs
                if (isset($existingNode['childs'])) {
                  $existingNode['childs'][] = $node[$existingTrigger];
                } else {
                  $existingNode = ['childs' => array_merge([$existingNode], [$node[$existingTrigger]])];
                }
                unset($node[$existingTrigger]);
              }
            }
            if (!empty($node)) {
              $properties[$key]['effectPassive'] = array_merge($properties[$key]['effectPassive'], $node);
            }
          } else {
            $properties[$key] = array_merge($properties[$key], $node);
          }
        } else {
          // PassiveEffects
          // we need to merge per trigger if needed
          foreach ($properties[$key] as $existingTrigger => &$existingNode) {
            // Nothing to merge as it doesn't exist
            if (!isset($node[$existingTrigger])) {
              continue;
            }
            // throw new \feException("titi");
            // we already have childs
            if (isset($existingNode['childs'])) {
              $existingNode['childs'][] = $node[$existingTrigger];
            } else {
              $existingNode = ['childs' => array_merge([$existingNode], [$node[$existingTrigger]])];
            }
            unset($node[$existingTrigger]);
          }
          if (!empty($node)) {
            $properties[$key] = array_merge($properties[$key], $node);
          }
        }
      } else {
        if ($key == 'effectInfinity' && isset($calculated['trigger'])) {
          $properties[$key]['effectPassive'] = $node;
        } else {
          $properties[$key] = $node;
        }
      }
    }

    if (isset($calculated['outputPassive'])) {
      // add the condition to the passive effect, if it exists
      // if (isset($calculated['triggerConditions'])) {
      foreach ($calculated['outputPassive'] as $trigger => &$passive) {
        $conditions = [];
        if (isset($passive['condition'])) {
          $conditions[] = $passive['condition'];
          unset($passive['condition']);
        }
        $passive['conditions'] = array_merge(
          $passive['conditions'] ?? [],
          $conditions,
          $calculated['triggerConditions'] ?? [],
          $calculated['conditionConditions'] ?? []
        );
      }
      // }
      if (isset($properties[$key])) {
        foreach ($properties[$key] as $existingTrigger => &$existingNode) {
          // Nothing to merge as it doesn't exist
          if (!isset($calculated['outputPassive'][$existingTrigger])) {
            continue;
          }

          // update of existing node as there is still an output
          if ($trinity['output'] == 478) {
            self::addEffectToCondition($calculated['outputPassive'][$existingTrigger]['output'], $existingNode);
            continue;
          }
          // we already have childs
          if (isset($existingNode['childs'])) {
            $existingNode['childs'][] = $calculated['outputPassive'][$existingTrigger];
          } else {
            $existingNode = ['childs' => array_merge([$existingNode], [$calculated['outputPassive'][$existingTrigger]])];
          }
          unset($calculated['outputPassive'][$existingTrigger]);
        }
      }
      $properties['effectPassive'] = array_merge($properties['effectPassive'] ?? [], $calculated['outputPassive']);
    }

    // Description
    $keyDesc = in_array($key, ['effectSupport', 'effectInfinity']) ? 'supportDesc' : 'effectDesc';
    if (!empty($properties[$keyDesc])) {
      $properties[$keyDesc][] = '<BR>';
    }
    $properties[$keyDesc][] = $calculated['triggerDescription'] ?? '';
    $properties[$keyDesc][] = $calculated['conditionDescription'] ?? '';
    $properties[$keyDesc][] = $calculated['outputDescription'] ?? '';

    if ($key == 'effectSupport') {
      if ($calculated['triggerDescription'] == '{D}') {
        $properties['supportIcon'] = 'discard';
      }
    } elseif ($key == 'effectInfinity') {
      $properties['supportIcon'] = 'infinity';
    }

    // debug
    // throw new \feException(print_r($properties));
    //$properties['calculated'] = $calculated;

    // use calculated to generate the effect in properties
    // if conditionEffect dans condition => noeud SEQ
    // Trigger condition => vrai check condition ! Array
    // le OUTPUT (s'il existe) doit être remplacé par l'output

    // TOOD: add description
  }

  public static function computeTrigger($effect, &$calculated)
  {
    $trigger = self::getTriggers()[$effect] ?? null;

    if (is_null($trigger)) {
      throw new \Bga\GameFramework\VisibleSystemException('Unique trigger not implemented.' . $effect);
    }

    $calculated['type'] = $trigger['type'] ?? 'effectPassive';
    if (isset($trigger['trigger']) && $trigger['trigger'] != '') {
      $calculated['trigger'] = $trigger['trigger'];
    }
    if (isset($trigger['condition'])) {
      $calculated['triggerConditions'] = array_merge(
        $calculated['triggerConditions'] ?? [],
        is_array($trigger['condition']) ? $trigger['condition'] : [$trigger['condition']]
      );
    }
    if (isset($trigger['description'])) {
      $calculated['triggerDescription'] = $trigger['description'];
    }
    if (isset($trigger['afterRest'])) {
      $calculated['afterRest'] = true;
    } else {
      $calculated['afterRest'] = false;
    }
    if (isset($trigger['n'])) {
      $calculated['conditionN'] = $trigger['n'];
    }
    if (isset($trigger['pId'])) {
      $calculated['pId'] = $trigger['pId'];
    }
    if (isset($trigger['listeningConditions'])) {
      $calculated['triggerListeningConditions'] = $trigger['listeningConditions'];
    }
  }

  public static function computeConditions($effect, &$calculated)
  {
    $conditions = self::getConditions()[$effect] ?? null;

    if (is_null($conditions)) {
      throw new \Bga\GameFramework\VisibleSystemException('Unique conditions not implemented.' . $effect);
    }
    if (isset($conditions['description'])) {
      $calculated['conditionDescription'] = $conditions['description'];
    }

    if (isset($conditions['condition'])) {
      if ($calculated['type'] != 'effectPassive') {
        $calculated['triggerConditions'] = array_merge(
          $calculated['triggerConditions'] ?? [],
          is_array($conditions['condition']) ? $conditions['condition'] : [$conditions['condition']]
        );
      } else {
        // changed due to otherwise effect
        $calculated['conditionConditions'] = array_merge(
          $calculated['conditionConditions'] ?? [],
          is_array($conditions['condition']) ? $conditions['condition'] : [$conditions['condition']]
        );
      }
    }

    if (isset($conditions['effect'])) {
      $calculated['conditionEffect'] = $conditions['effect'];
    }

    if (isset($conditions['passiveEffect'])) {
      $calculated['passiveEffect'] = $conditions['passiveEffect'];
    }
    $calculated['ifYouDo'] = $conditions['ifYouDo'] ?? false;
  }

  public static function computeOutput($effect, &$calculated)
  {
    $output = self::getOutput()[$effect] ?? null;

    if (is_null($output)) {
      throw new \Bga\GameFramework\VisibleSystemException('Unique conditions not implemented.' . $effect);
    }
    if (isset($output['description'])) {
      $calculated['outputDescription'] = $output['description'];
    }

    if (isset($output['output'])) {
      $calculated['output'] = $output['output'];
    }
    if (isset($output['passive'])) {
      $calculated['outputPassive'] = $output['passive'];
    }
    if (isset($output['attributes'])) {
      $calculated['outputAttributes'] = $output['attributes'];
    }
    // For No trigger effects,
    if (isset($output['noTrigger'])) {
      $calculated['noTrigger'] = true;
    }
    if (isset($output['oppositeOutput'])) {
      $calculated['oppositeOutput'] = $output['oppositeOutput'];
    }

    // manage unique power attributes (tough/gigantic/) awaiting info from GDs
  }

  public static function insertCheckCondition($conditions, &$node, $description)
  {
    if (empty($node)) {
      $node = FT::ACTION(CHECK_CONDITION, [
        'conditions' => $conditions,
        'effect' => 'OUTPUT',
        'oppositeEffect' => 'OPPOSITE',
        'description' => $description,
      ]);
    } else {
      $nKey = Utils::search($node, function ($child) {
        return ($child['action'] ?? '') == CHECK_CONDITION;
      });
      $node[$nKey]['conditions'] = array_merge(
        is_array($node[$nKey]['conditions']) ? $node[$nKey]['conditions'] : [$node[$nKey]['conditions']],
        $conditions
      );
    }
  }

  public static function addEffectToCondition($effect, &$node)
  {
    if (empty($node)) {
      $node = $effect;
    } else {
      $node = Utils::updateTree($node, 'OUTPUT', $effect);
    }
  }

  public static function addOppositeToNode($effect, &$node)
  {
    if (empty($node)) {
      $node = $effect;
    } else {
      $node = Utils::updateTree($node, 'OPPOSITE', $effect);
    }
  }

  // TODO: to replace with previous function
  public static function addOutputToNode($effect, &$node)
  {
    if (empty($node)) {
      $node = $effect;
    } else {
      $node = Utils::updateTree($node, 'OUTPUT', $effect);
    }
  }

  /**
   * Dynamic attributes (noTrigger outputs) are checked at runtime via checkAttributeCondition,
   * without an event context. Event-based trigger conditions must be mapped to state checks.
   */
  private static function getDynamicAttributeConditionList($trinity, $calculated, $unlessConditions)
  {
    $triggerId = $trinity['trigger'] ?? null;
    $stateTriggerConditions = self::getStateTriggerConditionsForDynamicAttribute($triggerId);
    if ($stateTriggerConditions !== null) {
      return array_merge($stateTriggerConditions, $unlessConditions);
    }

    return array_merge($calculated['triggerConditions'] ?? [], $unlessConditions);
  }

  /**
   * State-based equivalents for triggers that normally rely on ChooseAssignment event data.
   */
  private static function getStateTriggerConditionsForDynamicAttribute($triggerId)
  {
    // Another Character joined my Expedition — at least two Characters in this Expedition.
    if (in_array($triggerId, [21, 253, 419, 517])) {
      return ['XCharacterInExpedition:2:LTE'];
    }

    return null;
  }

  // public static function getTrigger($effect){
  //   $trigger = null;
  //   switch ($effect) {
  //     case 2:

  //       break ;
  //   }
  // }
}
