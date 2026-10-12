<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Altered implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * Altered game states description
 *
 */

$machinestates = [
  // The initial state. Please do not modify.
  ST_GAME_SETUP => [
    'name' => 'gameSetup',
    'description' => '',
    'type' => 'manager',
    'action' => 'stGameSetup',
    'transitions' => ['' => ST_PRECO_DECK_SELECTION],
    // 'transitions' => ['' => 50],
  ],

  // USEFUL FOR DEBUGGING GAME CREATION
  50 => [
    'name' => 'plop',
    'description' => 'Foo',
    'descriptionmyturn' => 'Foo',
    'type' => 'activeplayer',
    'transitions' => ['' => ST_DECK_SELECTION],
  ],

  ST_GENERIC_NEXT_PLAYER => [
    'name' => 'genericNextPlayer',
    'type' => 'game',
    'description' => '',
  ],

  ST_PRECO_DECK_SELECTION => [
    'name' => 'selectPrecoDeck',
    'description' => clienttranslate('Waiting for others to choose their faction'),
    'descriptionmyturn' => clienttranslate('${you} must select the faction you want to play with'),
    'type' => 'multipleactiveplayer',
    'args' => 'argsPrecoDeckSelection',
    'possibleactions' => ['actSelectPrecoDeck', 'actSelectRandomDeck', 'actConfirmAPIDeck', 'actLoadAPIDecks', 'actCancelPrecoDeckSelection'],
    'transitions' => ['done' => ST_SETUP, 'zombiePass' => ST_SETUP],
  ],

  ST_DECK_SELECTION => [
    'name' => 'selectDeck',
    'description' => clienttranslate('Waiting for others to choose their deck'),
    'descriptionmyturn' => clienttranslate('${you} must select the deck you want to play'),
    'type' => 'multipleactiveplayer',
    // 'args' => 'argsDeckSelection',
    'possibleactions' => ['actSelectDeck', 'actCancelDeckSelection'],
    'transitions' => ['done' => ST_SETUP, 'zombiePass' => ST_SETUP],
  ],

  ST_SETUP => [
    'name' => 'deckSetup',
    'type' => 'game',
    'action' => 'stDeckSetup',
    'description' => '',
    'transitions' => ['' => ST_FIRST_DAY],
  ],

  /////////////////////////////////////////////////
  //  _   _                 ____
  // | \ | | _____      __ |  _ \  __ _ _   _
  // |  \| |/ _ \ \ /\ / / | | | |/ _` | | | |
  // | |\  |  __/\ V  V /  | |_| | (_| | |_| |
  // |_| \_|\___| \_/\_/   |____/ \__,_|\__, |
  //                                    |___/
  /////////////////////////////////////////////////

  ST_FIRST_DAY => [
    'name' => 'firstDay',
    'description' => '',
    'type' => 'game',
    'action' => 'stFirstDay',
    'transitions' => [
      'done' => ST_FIRST_DAY_MULTI,
    ],
  ],

  ST_FIRST_DAY_MULTI => [
    'name' => 'firstDayManaSelection',
    'type' => 'multipleactiveplayer',
    'description' => clienttranslate('Waiting for other players to select cards to discard as mana'),
    'descriptionmyturn' => clienttranslate('${you} must select ${n} cards to discard as mana'),
    'args' => 'argsFirstDayManaSelection',
    'possibleactions' => ['actFirstDayManaSelection', 'actCancelFirstDayManaSelection'],
    'transitions' => ['done' => ST_BEFORE_ASSIGNMENT, 'zombiePass' => ST_BEFORE_ASSIGNMENT],
  ],

  ST_NEW_DAY => [
    'name' => 'newDay',
    'description' => '',
    'type' => 'game',
    'action' => 'stNewDay',
  ],

  //////////////////////////////
  //  _____
  // |_   _|   _ _ __ _ __
  //   | || | | | '__| '_ \
  //   | || |_| | |  | | | |
  //   |_| \__,_|_|  |_| |_|
  //////////////////////////////

  ST_BEFORE_ASSIGNMENT => [
    'name' => 'beforeAssignment',
    'description' => '',
    'type' => 'game',
    'action' => 'stBeforeAssignment',
    'updateGameProgression' => true,
  ],

  ST_ASSIGNMENT => [
    'name' => 'assigment',
    'description' => '',
    'type' => 'game',
    'action' => 'stAssignment',
    'transitions' => [
      'done' => ST_PRE_DUSK_PHASE,
    ],
  ],

  ST_PRE_DUSK_PHASE => [
    'name' => 'beforeDusk',
    'description' => '',
    'type' => 'game',
    'action' => 'stBeforeDusk',
    'transitions' => [
      'done' => ST_DUSK,
    ],
  ],

  ST_DUSK => [
    'name' => 'dusk',
    'description' => '',
    'type' => 'game',
    'action' => 'stDusk',
    'transitions' => [
      'done' => ST_AFTER_DUSK,
    ],
  ],

  ST_AFTER_DUSK => [
    'name' => 'afterDusk',
    'description' => '',
    'type' => 'game',
    'action' => 'stAfterDusk',
    'transitions' => [
      'done' => ST_BEFORE_NIGHT,
    ],
  ],

  ST_BEFORE_NIGHT => [
    'name' => 'beforeNight',
    'description' => '',
    'updateGameProgression' => true,
    'type' => 'game',
    'action' => 'stBeforeNight',
    'transitions' => [
      'done' => ST_NIGHT_CLEANUP,
      'newDay' => ST_BEFORE_ASSIGNMENT,
    ],
  ],

  ST_END_OF_NIGHT => [
    'name' => 'endOfNight',
    'description' => '',
    'type' => 'game',
    'action' => 'stEndOfNight',
    'transitions' => [
      'newDay' => ST_BEFORE_ASSIGNMENT,
    ],
  ],

  ////////////////////////////////////////////////////////////////////////////
  //     _   _                  _         _        _   _
  //    / \ | |_ ___  _ __ ___ (_) ___   / \   ___| |_(_) ___  _ __  ___
  //   / _ \| __/ _ \| '_ ` _ \| |/ __| / _ \ / __| __| |/ _ \| '_ \/ __|
  //  / ___ \ || (_) | | | | | | | (__ / ___ \ (__| |_| | (_) | | | \__ \
  // /_/   \_\__\___/|_| |_| |_|_|\___/_/   \_\___|\__|_|\___/|_| |_|___/
  //
  ////////////////////////////////////////////////////////////////////////////

  ST_CHOOSE_ASSIGNMENT => [
    'name' => 'chooseAssignment',
    'description' => clienttranslate('${actplayer} must choose an action or pass'),
    'descriptionmyturn' => clienttranslate('${you} must choose an action or pass'),
    'descriptionmyturnadditional' => clienttranslate('${you} may play a card'),
    'descriptionadditional' => clienttranslate('${actplayer} may play a card'),
    'args' => 'argsAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actPlay', 'actSupport', 'actTap', 'actPass', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_GAIN => [
    'name' => 'gain',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPassOptionalAction'],
  ],

  ST_LOOSE => [
    'name' => 'loose',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ST_DRAW => [
    'name' => 'draw',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ST_SPECIAL_EFFECT => [
    'name' => 'specialEffect',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ST_CHECK_CONDITION => [
    'name' => 'checkCondition',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'description' => '',
    'possibleactions' => ['actPassOptionalAction'],
  ],

  ST_AFTER_YOU => [
    'name' => 'afterYou',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ST_DRAW_MANA => [
    'name' => 'drawMana',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ST_USE_COUNTER => [
    'name' => 'useCounter',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ST_ROLL_DIE => [
    'name' => 'rollDie',
    'type' => 'activeplayer',
    'action' => 'stAtomicAction',
    'description' => clienttranslate('${actplayer} must choose 1 die result'),
    'descriptionmyturn' => clienttranslate('${you} must choose 1 die result'),
    'descriptionbastion' => clienttranslate('${actplayer} must choose 1 die result or select a card to add 2'),
    'descriptionmyturnbastion' => clienttranslate('${you} must choose 1 die result or select a card to add 2'),
    'args' => 'argsAtomicAction',
    'possibleactions' => ['actRollDie', 'actDiscardAdd', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_RESUPPLY => [
    'name' => 'resupply',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ST_MOVE_EXPEDITION => [
    'name' => 'moveExpedition',
    'description' => clienttranslate('${actplayer} must move one expedition forward'),
    'descriptionmyturn' => clienttranslate('${you} must move one expedition forward'),
    'descriptionbackward' => clienttranslate('${actplayer} must move one expedition backward'),
    'descriptionmyturnbackward' => clienttranslate('${you} must move one expedition backward'),
    'type' => 'activeplayer',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveExpedition', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_DISCARD => [
    'name' => 'discard',
    'description' => clienttranslate('${actplayer} must discard ${n} card(s) from ${source} to ${destination}'),
    'descriptionmyturn' => clienttranslate('${you} must discard ${n} card(s) from ${source} to ${destination}'),
    'descriptionCanPass' => clienttranslate('${actplayer} may discard ${n} card(s) from ${source} to ${destination}'),
    'descriptionmyturnCanPass' => clienttranslate('${you} may discard ${n} card(s) from ${source} to ${destination}'),
    'descriptiondownTo' => clienttranslate('${actplayer} must discard down to ${downTo} card(s) in ${source}'),
    'descriptionmyturndownTo' => clienttranslate('${you} must discard down to ${downTo} card(s) in ${source}'),
    'descriptionnightCleanUp' => clienttranslate(
      '${actplayer} must discard ${n} reserve card(s) and ${nLandmarks} landmark card(s)'
    ),
    'descriptionmyturnnightCleanUp' => clienttranslate(
      '${you} must discard ${n} reserve card(s) and ${nLandmarks} landmark card(s)'
    ),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actDiscard', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
    'type' => 'activeplayer',
  ],
  ST_NIGHT_CLEANUP => [
    'name' => 'nightCleanup',
    'description' => clienttranslate(
      '${actplayer} must discard ${nReserve} reserve card(s) and ${nLandmarks} landmark card(s)'
    ),
    'descriptionmyturn' => clienttranslate(
      '${you} must discard ${nReserve} reserve card(s) and ${nLandmarks} landmark card(s)'
    ),
    'descriptionlandmarksOnly' => clienttranslate(
      '${actplayer} must discard ${nLandmarks} landmark card(s)'
    ),
    'descriptionmyturnlandmarksOnly' => clienttranslate(
      '${actplayer} must discard ${nLandmarks} landmark card(s)'
    ),
    'descriptionreserveOnly' => clienttranslate(
      '${actplayer} must discard ${nReserve} reserve card(s)'
    ),
    'descriptionmyturnreserveOnly' => clienttranslate(
      '${you} must discard ${nReserve} reserve card(s)'
    ),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actNightCleanup'],
    'type' => 'activeplayer',
  ],

  ST_TARGET => [
    'name' => 'target',
    // 'description' => clienttranslate('${actplayer} must target ${n} card(s)}'),
    // 'descriptionmyturn' => clienttranslate('${you} must target ${n} card(s)'),
    'description' => clienttranslate('${actplayer} must select the target(s): ${description}'),
    'descriptionmyturn' => clienttranslate('${you} must select the target(s): ${description}'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actTarget', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  ST_DISCARD_DO => [
    'name' => 'discardDo',
    // 'description' => clienttranslate('${actplayer} must target ${n} card(s)}'),
    // 'descriptionmyturn' => clienttranslate('${you} must target ${n} card(s)'),
    'description' => clienttranslate('${actplayer} may discard cards to ${effect_desc}'),
    'descriptionmyturn' => clienttranslate('${you} may discard cards to ${effect_desc}'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actTarget', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  ST_TARGET_PLAYER => [
    'name' => 'targetPlayer',
    'description' => '${actplayer} ${description}',
    'descriptionmyturn' => '${you} ${description}',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actTargetPlayer', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  ST_SPELL_CLEANUP => [
    'name' => 'spellCleanup',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ST_INVOKE_TOKEN => [
    'name' => 'invokeToken',
    'description' => clienttranslate('${actplayer} may invoke ${tokenType}'),
    'descriptionmyturn' => clienttranslate('${you} may invoke ${tokenType}'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actInvokeToken', 'actInvokeTokenPass', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_BLOCK_EXPEDITION => [
    'name' => 'blockExpedition',
    'description' => clienttranslate('${actplayer} must block an expedition from moving this day'),
    'descriptionmyturn' => clienttranslate('${you} must block an expedition from moving this day'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actBlockExpedition', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_TARGET_EXPEDITION => [
    'name' => 'targetExpedition',
    'description' => clienttranslate('${actplayer} must target ${n} expedition(s)'),
    'descriptionmyturn' => clienttranslate('${you} must target ${n} expedition(s)'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actTargetExpedition', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_PLAY_CARD => [
    'name' => 'playCard',
    'description' => clienttranslate('${actplayer} may play ${card_name}'),
    'descriptionmyturn' => clienttranslate('${you} may play ${card_name}'),
    'descriptionfree' => clienttranslate('${actplayer} may play ${card_name} for free'),
    'descriptionmyturnfree' => clienttranslate('${you} may play ${card_name} for free'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actPlayCard', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  ST_ACTIVATE_CARD => [
    'name' => 'activateCard',
    'description' => '',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'transitions' => [],
    'possibleactions' => ['actPassOptionalAction'],
  ],

  ST_MOVE_CARD => [
    'name' => 'moveCard',
    'description' => '',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'transitions' => [],
  ],

  ST_ACTIVATE_EFFECT => [
    'name' => 'activateEffect',
    'description' => '',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'transitions' => [],
    'possibleactions' => ['actPassOptionalAction'],
  ],

  ST_TAP => [
    'name' => 'tap',
    'description' => '',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'transitions' => [],
    'possibleactions' => ['actPassOptionalAction'],
  ],

  ST_PAY => [
    'name' => 'pay',
    'description' => clienttranslate('${actplayer} must pay mana'),
    'descriptionmyturn' => clienttranslate('${you} must pay mana'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'transitions' => [],
    'possibleactions' => ['actPay', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  ST_EXHAUST => [
    'name' => 'exhaust',
    'description' => '',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'transitions' => [],
    'possibleactions' => ['actPassOptionalAction'],
  ],

  ST_READY => [
    'name' => 'ready',
    'description' => '',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'transitions' => [],
    'possibleactions' => ['actPassOptionalAction'],
  ],

  ST_EXCHANGE => [
    'name' => 'exchange',
    'description' => clienttranslate('${actplayer} must select 1 card from Hand and Reserve to switch position'),
    'descriptionmyturn' => clienttranslate('${you} must select 1 card from Hand and Reserve to switch position'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actExchange', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  // Bise
  ST_SPEND => [
    'name' => 'spend',
    'type' => 'activeplayer',
    'action' => 'stAtomicAction',
    'description' => clienttranslate('${actplayer} spends 1 counter'),
    'descriptionmyturn' => clienttranslate('${you} spends 1 counter'),
    'descriptionchoice' =>  clienttranslate('${actplayer} spends X counters to ${effect_desc}'),
    'descriptionmyturnchoice' =>  clienttranslate('${you} spends X counters to ${effect_desc}'),
    'args' => 'argsAtomicAction',
    'possibleactions' => ['actSpend', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_BOOST_EXCHANGE => [
    'name' => 'boostExchange',
    'type' => 'activeplayer',
    'action' => 'stAtomicAction',
    'description' => clienttranslate('${source}: ${actplayer} exchange boosts on 2 cards'),
    'descriptionmyturn' => clienttranslate('${source}: ${you} exchange boosts on 2 cards'),
    'args' => 'argsAtomicAction',
    'possibleactions' => ['actBoostExchange', 'actConfirmTurn', 'actRestart', 'actPassOptionalAction'],
  ],

  // Cyclone
  ST_END_AFTERNOON => [
    'name' => 'endAfternoon',
    'description' => '',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'transitions' => [],
    'possibleactions' => ['actPassOptionalAction'],
  ],

  ST_MARK_REGION => [
    'name' => 'markRegion',
    'description' => clienttranslate('${actplayer} may mark a visible region'),
    'descriptionmyturn' => clienttranslate('${you} may mark a visible region'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actMarkRegion', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  ST_MOVE_REGION_MARKER => [
    'name' => 'moveRegionMarker',
    'description' => clienttranslate('${actplayer} may move a ${markerType} terrain marker to its region'),
    'descriptionmyturn' => clienttranslate('${you} may move a ${markerType} terrain marker to its region'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actMoveRegionMarker', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  ST_INTERRUPT_REVEAL => [
    'name' => 'interuptReveal',
    'description' => clienttranslate('${actplayer} must confirm that the card is seen'),
    'descriptionmyturn' => clienttranslate('${you} must confirm that the card is seen'),
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'type' => 'activeplayer',
    'possibleactions' => ['actInteruptReveal', 'actPassOptionalAction', 'actConfirmTurn', 'actRestart'],
  ],

  ST_SHUFFLE => [
    'name' => 'shuffle',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],
  
  ST_COMPLETE_FEAT => [
    'name' => 'completeFeat',
    'description' => '',
    'type' => 'game',
    'action' => 'stAtomicAction',
    'transitions' => [],
    'possibleactions' => ['actPassOptionalAction'],
  ],

  // Eole
  ST_DISCARD_FROM_DECK => [
    'name' => 'discardFromDeck',
    'type' => 'game',
    'description' => '',
    'action' => 'stAtomicAction',
  ],

  ////////////////////////////////////
  //  _____             _
  // | ____|_ __   __ _(_)_ __   ___
  // |  _| | '_ \ / _` | | '_ \ / _ \
  // | |___| | | | (_| | | | | |  __/
  // |_____|_| |_|\__, |_|_| |_|\___|
  //              |___/
  ////////////////////////////////////
  ST_RESOLVE_STACK => [
    'name' => 'resolveStack',
    'type' => 'game',
    'description' => '',
    'action' => 'stResolveStack',
    'transitions' => [],
  ],

  ST_CONFIRM_TURN => [
    'name' => 'confirmTurn',
    'description' => clienttranslate('${actplayer} must confirm or restart their turn'),
    'descriptionmyturn' => clienttranslate('${you} must confirm or restart your turn'),
    'type' => 'activeplayer',
    'args' => 'argsConfirmTurn',
    'action' => 'stConfirmTurn',
    'possibleactions' => ['actConfirmTurn', 'actRestart'],
    'transitions' => ['endOfDay' => ST_CONFIRM_TURN],
  ],

  ST_CONFIRM_PARTIAL_TURN => [
    'name' => 'confirmPartialTurn',
    'description' => clienttranslate('${actplayer} must confirm the switch of player'),
    'descriptionmyturn' => clienttranslate('${you} must confirm the switch of player. You will not be able to restart turn'),
    'type' => 'activeplayer',
    'args' => 'argsConfirmTurn',
    // 'action' => 'stConfirmPartialTurn',
    'possibleactions' => ['actConfirmPartialTurn', 'actRestart'],
  ],

  ST_RESOLVE_CHOICE => [
    'name' => 'resolveChoice',
    'description' => clienttranslate('${actplayer} must choose which effect to resolve'),
    'descriptionmyturn' => clienttranslate('${you} must choose which effect to resolve'),
    'descriptionxor' => clienttranslate('${actplayer} must choose exactly one effect'),
    'descriptionmyturnxor' => clienttranslate('${you} must choose exactly one effect'),
    'descriptionremaining' => clienttranslate('${actplayer} must choose ${nRemaining}'),
    'descriptionmyturnremaining' => clienttranslate('${you} must choose ${nRemaining}'),
    'type' => 'activeplayer',
    'args' => 'argsResolveChoice',
    'action' => 'stResolveChoice',
    'possibleactions' => ['actChooseAction', 'actRestart'],
    'transitions' => [],
  ],

  ST_IMPOSSIBLE_MANDATORY_ACTION => [
    'name' => 'impossibleAction',
    'description' => clienttranslate('${actplayer} can\'t take the mandatory action and must restart his turn'),
    'descriptionmyturn' => clienttranslate(
      '${you} can\'t take the mandatory action. Restart your turn or exchange/cook to make it possible'
    ),
    'type' => 'activeplayer',
    'args' => 'argsImpossibleAction',
    'possibleactions' => ['actRestart'],
  ],

  // END OF GAME
  ST_PRE_END_OF_GAME => [
    'name' => 'preEndOfGame',
    'type' => 'game',
    'description' => '',
    'action' => 'stPreEndOfGame',
    'updateGameProgression' => true,
    'transitions' => ['' => ST_END_GAME],
  ],

  // Final state.
  // Please do not modify (and do not overload action/args methods).
  ST_END_GAME => [
    'name' => 'gameEnd',
    'description' => clienttranslate('End of game'),
    'type' => 'manager',
    'action' => 'stGameEnd',
    'args' => 'argGameEnd',
  ],
];
