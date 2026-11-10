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
 * gameoptions.inc.php
 *
 * Altered game options description
 *
 * In this file, you can define your game options (= game variants).
 *
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in altered.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

namespace ALT;

require_once 'modules/php/constants.inc.php';
$game_options = [
  // OPTION_BEGINNER => [
  //   'name' => clienttranslate('Beginner mode'),
  //   'default' => OPTION_ENABLED,
  //   'values' => [
  //     OPTION_DISABLED => [
  //       'name' => 'Disabled',
  //       'tmdisplay' => totranslate('Standard'),
  //       'description' => totranslate('Custom & starter decks can be used'),
  //       'nobeginner' => true,
  //     ],
  //     OPTION_ENABLED => [
  //       'name' => 'Enabled',
  //       'tmdisplay' => totranslate('Beginner mode'),
  //       'description' => totranslate('Game can be played only with starter decks'),
  //     ],
  //   ]
  // ],
  OPTION_UNDO => [
    'name' => clienttranslate('Undo'),
    'values' => [
      OPTION_DISABLED => [
        'name' => 'Disabled',
        'tmdisplay' => totranslate('no undo'),
        'description' => totranslate('Undo is disabled for everyone'),
      ],
      OPTION_ENABLED => [
        'name' => 'Enabled',
        'description' => totranslate('Undo can be disabled as a player preference'),
      ],
    ]
  ],
  OPTION_DECK_LOCK_TOURNAMENT => [
    'name' => clienttranslate('Decks lock for tournament only'),
    'values' => [
      OPTION_DISABLED => [
        'name' => 'Disabled',
        'tmdisplay' => totranslate("Decks aren't locked, participants can change between tournament games"),
        'description' => totranslate("Decks aren't locked, participants can change between tournament games"),
      ],
      OPTION_ENABLED => [
        'name' => 'Enabled',
        'description' => totranslate("Decks are locked, participants can't change between tournament games"),
      ],
    ]
  ]

];

$game_preferences = [
  OPTION_CONFIRM => [
    'name' => totranslate('Turn confirmation'),
    'needReload' => false,
    'default' => OPTION_CONFIRM_DISABLED,
    'values' => [
      OPTION_CONFIRM_ENABLED => ['name' => totranslate('Enabled')],
      OPTION_CONFIRM_DISABLED => ['name' => totranslate('Disabled')],
      OPTION_CONFIRM_TIMER => ['name' => totranslate('Enabled with timer')],
    ],
  ],
  OPTION_CONFIRM_UNDOABLE => [
    'name' => totranslate('Undoable actions confirmation'),
    'needReload' => false,
    'values' => [
      OPTION_CONFIRM_ENABLED => ['name' => totranslate('Enabled')],
      OPTION_CONFIRM_DISABLED => ['name' => totranslate('Disabled')],
    ],
  ],
  OPTION_PLAYER_UNDO => [
    'name' => totranslate('Undo:'),
    'needReload' => false,
    'values' => [
      OPTION_PLAYER_UNDO_ENABLED => ['name' => totranslate('Enabled')],
      OPTION_PLAYER_UNDO_DISABLED => ['name' => totranslate('Disabled')],
    ],
  ],
];
