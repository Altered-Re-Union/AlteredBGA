<?php

/*
 * Game options
 */

const OPTION_DECKS = 102;
const OPTION_DECKS_DEMO = 0;
const OPTION_DECKS_STARTER = 1;
const OPTION_UNDO = 151;
// const OPTION_COMPETITIVE_NORMAL = 2;
// const OPTION_COMPETITIVE_CUSTOM_SETUP = 3;
// const OPTION_COMPETITIVE_CUSTOM_SETUP_NON_BEGINNER = 4;
// const OPTION_COMPETITIVE_ALL_SAME_SETUP = 5;

// const OPTION_PEACEFUL_MODE = 103;
// const OPTION_PEACEFUL_MODE_DISABLED = 0;
// const OPTION_PEACEFUL_MODE_ENABLED = 1;

// const OPTION_SOLO_DIFFICULTY = 104;
// const OPTION_SOLO_DIFFICULTY_BEGINNER = 0;
// const OPTION_SOLO_DIFFICULTY_NORMAL = 1;
// const OPTION_SOLO_DIFFICULTY_HARD = 2;

// const OPTION_SOLO_CHALLENGE = 106;
// const OPTION_CHALLENGE_YES = 1;
// const OPTION_CHALLENGE_NO = 0;

const OPTION_DECK_FORMAT = 155;
const OPTION_DF_FRONTIER = 0;
const OPTION_DF_STANDARD = 1;
const OPTION_DF_SINGLETON = 2;
const OPTION_DF_SANDBOX = 3;
const OPTION_DF_NOUNIQUE = 4;
const OPTION_DF_DEMO = 5;
const OPTION_DF_TEST = 6;
const OPTION_DF_SEALED = 7;
// const OPTION_SAME_MAP_RANDOM = 0;

/*
 * User preferences
 */
const OPTION_CONFIRM = 103;
const OPTION_CONFIRM_DISABLED = 0;
const OPTION_CONFIRM_ENABLED = 2;
const OPTION_CONFIRM_TIMER = 3;

const OPTION_CONFIRM_UNDOABLE = 104;

const OPTION_PLAYER_UNDO = 152;
const OPTION_PLAYER_UNDO_ENABLED = 1;
const OPTION_PLAYER_UNDO_DISABLED = 2;

const OPTION_BEGINNER = 150;
const OPTION_DISABLED = 0;
const OPTION_ENABLED = 1;
const OPTION_DEMO = 2;
const OPTION_SO = 3;
const OPTION_SDU = 4;


/*
 * State constants
 */
const ST_GAME_SETUP = 1;
const ST_PRECO_DECK_SELECTION = 2;
const ST_DECK_SELECTION = 3;
const ST_SETUP = 5; // tempest setup + deck shuffle + alterer position

const ST_FIRST_DAY = 10;
const ST_FIRST_DAY_MULTI = 11;
const ST_NEW_DAY = 12; // Mana draw, if day 1 = 7 + 3 mana
const ST_NEW_DAY_MULTI = 13;
const ST_NEW_DAY_BONUS = 14;

const ST_BEFORE_ASSIGNMENT = 18;
const ST_ASSIGNMENT = 19;
const ST_CHOOSE_ASSIGNMENT = 20;

// Atomic action
const ST_DISCARD = 21;
const ST_GAIN = 22;
const ST_TARGET = 23;
const ST_LOOSE = 24;
const ST_SPELL_CLEANUP = 25;
const ST_INVOKE_TOKEN = 26;
const ST_ACTIVATE_CARD = 27;
const ST_DRAW = 28;
const ST_SPECIAL_EFFECT = 29;
const ST_CHECK_CONDITION = 30;
const ST_AFTER_YOU = 31;
const ST_ROLL_DIE = 32;
const ST_RESUPPLY = 33;
const ST_MOVE_EXPEDITION = 34;
const ST_USE_COUNTER = 35;
const ST_ACTIVATE_EFFECT = 36;
const ST_TAP = 37;
const ST_PLAY_CARD = 38;
const ST_MOVE_CARD = 39;
const ST_PAY = 40;
const ST_DRAW_MANA = 41;
const ST_BLOCK_EXPEDITION = 42;
const ST_TARGET_PLAYER = 43;
const ST_DISCARD_DO = 44;
const ST_TARGET_EXPEDITION = 45;
// Alizé
const ST_EXHAUST = 46;
const ST_READY = 47;
const ST_EXCHANGE = 48;
// Bise
const ST_SPEND = 49;
const ST_BOOST_EXCHANGE = 50;
// Cyclone
const ST_END_AFTERNOON = 51;
const ST_MARK_REGION = 52;
const ST_MOVE_REGION_MARKER = 53;
const ST_INTERRUPT_REVEAL = 54;
const ST_SHUFFLE = 55;
// Eole
const ST_COMPLETE_FEAT = 56;
const ST_DISCARD_FROM_DECK = 57;

const ST_PRE_DUSK_PHASE = 83; // some effects give choice before counting
const ST_DUSK = 84; // resolution of the tempest
const ST_AFTER_DUSK = 87;
const ST_BEFORE_NIGHT = 85;
const ST_NIGHT = 86; // clean up !! may need some choice
const ST_NIGHT_CLEANUP = 88;
const ST_END_OF_NIGHT = 89;
const ST_RESOLVE_STACK = 90;
const ST_RESOLVE_CHOICE = 91;
const ST_IMPOSSIBLE_MANDATORY_ACTION = 92;
const ST_CONFIRM_TURN = 93;
const ST_CONFIRM_PARTIAL_TURN = 94;

const ST_GENERIC_NEXT_PLAYER = 97;
const ST_PRE_END_OF_GAME = 98;
const ST_END_GAME = 99;

/*
 * ENGINE
 */
const NODE_SEQ = 'seq';
const NODE_OR = 'or';
const NODE_XOR = 'xor';
const NODE_PARALLEL = 'parallel';
const NODE_LEAF = 'leaf';

const ZOMBIE = 98;
const PASS = 99;

const AFTER_FINISHING_ACTION = 'afterFinishing';
const AFTER_FINISHING_ACTIVE = 'afterFinishingActive';
const AFTER_FINISHING_OPPONENT = 'afterFinishingOpponent';
const PRE_ACTION_DONE = 'preActionDone';

/*
 * Atomic action
 */

const ACTIVATE_CARD = 'ActivateCard';
const SPECIAL_EFFECT = 'SpecialEffect';
const CHOOSE_ASSIGNMENT = 'ChooseAssignment';
const GAIN = 'Gain';
const DISCARD = 'Discard';
const NIGHT_CLEANUP = 'NightCleanup';
const TARGET = 'Target';
const LOOSE = 'Loose';
const SPELL_CLEANUP = 'SpellCleanup';
const INVOKE_TOKEN = 'InvokeToken';
const DRAW = 'Draw';
const CHECK_CONDITION = 'CheckCondition';
const AFTER_YOU = 'AfterYou';
const ROLL_DIE = 'RollDie';
const RESUPPLY = 'Resupply';
const MOVE_EXPEDITION = 'MoveExpedition';
const USE_COUNTER = 'UseCounter';
const ACTIVATE_EFFECT = 'ActivateEffect';
const TAP = 'Tap';
const PLAY_CARD = 'PlayCard';
const MOVE_CARD = 'MoveCard';
const PAY = 'Pay';
const DRAW_MANA = 'DrawMana';
const BLOCK_EXPEDITION = 'BlockExpedition';
const TARGET_PLAYER = 'TargetPlayer';
const DISCARD_DO = 'DiscardDo';
const TARGET_EXPEDITION = 'TargetExpedition';
const EXHAUST = 'Exhaust';
const READY = 'Ready';
const EXCHANGE = 'Exchange';
const SPEND = 'Spend';
const BOOST_EXCHANGE = 'BoostExchange';
const END_AFTERNOON = 'EndAfternoon';
const MARK_REGION = 'MarkRegion';
const MOVE_REGION_MARKER = 'MoveRegionMarker';
const INTERUPT_REVEAL = 'InteruptReveal';
const SHUFFLE = 'Shuffle';
const COMPLETE_FEAT = 'CompleteFeat';
const DISCARD_FROM_DECK = 'DiscardFromDeck';

///// Action ARGS /////
const ARG_PLAYERS = 'players';
const ARG_N = 'n';

////////////// Flow convertor constants
const TARGET_ALL_CHARACTER = 'target_all_character';
const TARGET_ALL_CHARACTER_2 = 'target_all_character_2';
const TARGET_ALL_ALL_1_4 = 'target_all_all_1_4';
const DISCARD_HAND = 'discard_hand'; // Discard to hand
const SABOTAGE = 'sabotage';

const ACTIVE = 1;
const INACTIVE = 0;

const INFTY = 1000;
const MOUNTAIN = 'mountain';
const FOREST = 'forest';
const OCEAN = 'ocean';
const EFFECT = 'EFFECT';

const HERO = 'hero';
const CHARACTER = 'character';
const TOKEN = 'token';
const PERMANENT = 'permanent';
const SPELL = 'spell';
const TYPES = [HERO, CHARACTER, TOKEN, PERMANENT, SPELL];

const EVERYONE_ELSE = 'everyone-else';

const HAND = 'hand';
const RESERVE = 'reserve';
const LIMBO = 'limbo';
const MANA = 'mana';
const LANDMARK = 'landmark';
const DISCARD_PILE = 'discard'; // DISCARD is the action, DISCARD_PILE the location
const TOP_OF_DECK = 'topOfDeck';

const PHASE_MORNING = 'morning';
const PHASE_NOON = 'noon';
const PHASE_AFTERNOON = 'afternoon';
const PHASE_DUSK = 'dusk';
const PHASE_NIGHT = 'night';
const ASCEND = 'ascend';
/** Meeple on a Feat card that has been completed (visual marker). */
const FEAT_COMPLETED = 'featCompleted';

const STORM_LEFT = 'stormLeft';
const STORM_RIGHT = 'stormRight';
const STORMS = [STORM_LEFT, STORM_RIGHT];
const IN_PLAY = [STORM_LEFT, STORM_RIGHT, LANDMARK];
const CONTROLLED_RESERVE = [STORM_LEFT, STORM_RIGHT, LANDMARK, RESERVE, 'board-hero-%'];

const STORM_CARDS = [
  [[], [FOREST, MOUNTAIN, OCEAN]],
  [[FOREST], [MOUNTAIN, OCEAN]],
  [[MOUNTAIN], [FOREST, OCEAN]],
  [[OCEAN], [MOUNTAIN, FOREST]],
  [[FOREST, MOUNTAIN, OCEAN], []],
];
const STORM_BACK = 6;

const RARITY_COMMON = 0;
const RARITY_RARE = 1;
const RARITY_UNIQUE = 2;
const RARITY_EXALTED = 3;

const FACTION_AX = 'AX';
const FACTION_BR = 'BR';
const FACTION_LY = 'LY';
const FACTION_MU = 'MU';
const FACTION_OD = 'OD';
const FACTION_YZ = 'YZ';
const FACTION_NE = 'NE';
const FACTIONS = [FACTION_AX, FACTION_BR, FACTION_LY, FACTION_MU, FACTION_OD, FACTION_YZ];
// const FACTIONS = [FACTION_BR, FACTION_MU, FACTION_OD, FACTION_LY, FACTION_YZ];

const OPPONENT = 'opponent';
const ME = 'me';
const ALL = 'all';

/*********************
 ****** MEEPLES ******
 ********************/
const COMPANION = 'companion';

/*********************
 ***** SUB-TYPE **********
 *********************/
const DIVINITY = 'divinity';
const ADVENTURER = 'adventurer';
const PILOT = 'pilot';
const ENGINEER = 'engineer';
const ROBOT = 'robot';
const ELEMENTAL = 'elemental';
const DISRUPTION = 'disruption';
const TITAN = 'titan';
const CAT = 'cat';
const TRAINER = 'trainer';
const BLADEMASTER = 'blade master';
const SUPPORT = 'support';
const SQUIRREL = 'squirrel';
const DEMON = 'demon';
const ARTIST = 'artist';
const CITIZEN = 'citizen';
const DRUID = 'druid';
const PLANT = 'plant';
const SPIRIT = 'spirit';
const SOLDIER = 'soldier';
const BUREAUCRAT = 'bureaucrat';
const MAGE = 'mage';

const NOBLE = 'noble';
const SONG = 'song';
const LEVIATHAN = 'leviathan';
const DRAGON = 'dragon';
const DEITY = 'deity';
const MESSENGER = 'messenger';
const CONJURATION = 'conjuration';
const BOON = 'boon';
const FAIRY = 'fairy';
const APPRENTICE = 'apprentice';
const MANEUVER = 'maneuver';
const SCHOLAR = 'scholar';
const ANIMAL = 'animal';

// Alizé
const GEAR = 'gear';
const EXPEDITION = 'expedition';
const CONSTRUCTION = 'construction';
const SITE = 'site';
const ILLUSION = 'illusion';

// Cyclone
const ORE = 'ore';
const SCIENTIST = 'scientist';

// Duster
const SAP = 'sap';
const MERCHANT = 'merchant';
const ROGUE = 'rogue';

// EOLE
const FEAT = 'feat';
const CORRUPTION = 'corruption';

const SUBTYPES = [
  DIVINITY,
  ADVENTURER,
  PILOT,
  ENGINEER,
  ROBOT,
  ELEMENTAL,
  DISRUPTION,
  TITAN,
  CAT,
  TRAINER,
  BLADEMASTER,
  SUPPORT,
  SQUIRREL,
  DEMON,
  ARTIST,
  CITIZEN,
  DRUID,
  PLANT,
  SPIRIT,
  SOLDIER,
  BUREAUCRAT,
  MAGE,
  NOBLE,
  SONG,
  LEVIATHAN,
  DRAGON,
  DEITY,
  MESSENGER,
  CONJURATION,
  COMPANION,
  BOON,
  FAIRY,
  APPRENTICE,
  MANEUVER,
  SCHOLAR,
  ANIMAL,
  LANDMARK,
  ILLUSION,
  ORE,
  SCIENTIST,
  GEAR,
  EXPEDITION,
  CONSTRUCTION,
  SAP,
  MERCHANT,
  ROGUE,
  FEAT,
  CORRUPTION,
  FEAT
];

/*********************
 * ****** ABILITIES *****
 *********************/
const FLEETING = 'fleeting';
const BOOST = 'boost';
const GIGANTIC = 'gigantic';
const ANCHORED = 'anchored';
const ASLEEP = 'asleep';

const CONTROLLER = 'controller';
const OWNER = 'owner';

/*********************
 ******* MISC ********
 *********************/
const API_URL = 'https://api.equinox-ccg.io';

const DYNAMIC_PROPERTIES = ['tapped', 'extraDatas', 'setIcon', 'flavorText', 'asset', 'revealed', 'subtypes', 'typeline', 'fullArt', 'mainAsset'];
const UID_MAPPING = [
  'ALT_ALIZE_B_AX_01_C' => 'ALT_CORE_B_AX_01_C',
  'ALT_ALIZE_B_AX_03_C' => 'ALT_CORE_B_AX_03_C',
  'ALT_ALIZE_B_AX_31_C' => 'ALT_CORE_B_AX_31_C',
  'ALT_ALIZE_B_BR_01_C' => 'ALT_CORE_B_BR_01_C',
  'ALT_ALIZE_B_BR_03_C' => 'ALT_CORE_B_BR_03_C',
  'ALT_ALIZE_B_BR_31_C' => 'ALT_CORE_B_BR_31_C',
  'ALT_ALIZE_B_LY_02_C' => 'ALT_CORE_B_LY_02_C',
  'ALT_ALIZE_B_LY_03_C' => 'ALT_CORE_B_LY_03_C',
  'ALT_ALIZE_B_MU_02_C' => 'ALT_CORE_B_MU_02_C',
  'ALT_ALIZE_B_MU_03_C' => 'ALT_CORE_B_MU_03_C',
  'ALT_ALIZE_B_NE_02_C' => 'ALT_CORE_B_NE_02_C',
  'ALT_ALIZE_B_OR_01_C' => 'ALT_CORE_B_OR_01_C',
  'ALT_ALIZE_B_OR_03_C' => 'ALT_CORE_B_OR_03_C',
  'ALT_ALIZE_B_OR_31_C' => 'ALT_CORE_B_OR_31_C',
  'ALT_ALIZE_B_YZ_01_C' => 'ALT_CORE_B_YZ_01_C',
  'ALT_ALIZE_B_YZ_03_C' => 'ALT_CORE_B_YZ_03_C',
];

const MANUAL_ALT_ART = [
  'ALT_DUSTER_B_BR_83_C' => 'ALT_CYCLONE_B_BR_83_C',
];

/******************
 ****** STATS ******
 ******************/

const STAT_DAYS = 11;
const STAT_FACTION = 12;
const STAT_TURN = 13;
const STAT_WINNER = 14;
const STAT_MATCHUP = 15;
const STAT_GAME_WINNER = 16;
const STAT_CARD_1 = 21;
const STAT_CARD_2 = 22;
const STAT_CARD_3 = 23;
const STAT_CARD_4 = 24;
const STAT_CARD_5 = 25;
const STAT_CARD_6 = 26;
const STAT_CARD_7 = 27;
const STAT_CARD_8 = 28;
const STAT_CARD_9 = 29;
const STAT_CARD_10 = 30;
const STAT_CARD_11 = 31;
const STAT_CARD_12 = 32;
const STAT_CARD_13 = 33;
const STAT_CARD_14 = 34;
const STAT_CARD_15 = 35;
const STAT_CARD_16 = 36;
const STAT_CARD_17 = 37;
const STAT_CARD_18 = 38;
const STAT_CARD_19 = 39;
const STAT_CARD_20 = 40;
const STAT_CARD_21 = 41;
const STAT_CARD_22 = 42;
const STAT_CARD_23 = 43;
const STAT_CARD_24 = 44;
const STAT_CARD_25 = 45;
const STAT_CARD_26 = 46;
const STAT_CARD_27 = 47;
const STAT_CARD_28 = 48;
const STAT_CARD_29 = 49;
const STAT_CARD_30 = 50;
const STAT_CARD_31 = 51;
const STAT_CARD_32 = 52;
const STAT_CARD_33 = 53;
const STAT_CARD_34 = 54;
const STAT_CARD_35 = 55;
const STAT_CARD_36 = 56;
const STAT_CARD_37 = 57;
const STAT_CARD_38 = 58;
const STAT_CARD_39 = 59;
const STAT_CARD_40 = 60;

const STAT_HEROES = [
  1 => "AX_Common_SierraOddball",
  2 => "AX_Common_SubhashMarmo",
  3 => "AX_Common_TreystRossum",
  4 => "BR_Common_AtsadiSurge",
  5 => "BR_Common_BasiraKaizaimon",
  6 => "BR_Common_KojoBooda",
  7 => "LY_Common_AuraqKibble",
  8 => "LY_Common_FenCrowbar",
  9 => "LY_Common_NevenkaBlotch",
  10 => "MU_Common_ArjunSpike",
  11 => "MU_Common_RinOrchid",
  12 => "MU_Common_TeijaNauraa",
  13 => "OD_Common_GulrangTocsin",
  14 => "OD_Common_SigismarWingspan",
  15 => "OD_Common_WaruMack",
  16 => "YZ_Common_AfanasSenka",
  17 => "YZ_Common_AkeshaTaru",
  18 => "YZ_Common_LindiweMaw",
  19 => "AX_Common_IsareePebble",
  20 => "BR_Common_SolHalua",
  21 => "LY_Common_NadirBubbles",
  22 => "MU_Common_KauriPuff",
  23 => "OD_Common_ZhenZephyr",
  24 => "YZ_Common_MoyoSilk",
  25 => "AX_Common_DellaBolt",
  26 => "MU_Common_TuruunBenih",
  27 => "OD_Common_MatzHive"
];

/******************************
 ******* UNIQUE MANAGEMENT **** 
 ********************************/
const TRIGGER = [1, 2,  4, 5,  7, 8,  10, 11, 12, 13, 14, 15, 16, 17,  19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 192, 231, 236, 239, 240, 250, 253, 254, 419, 263, 256, 257, 372, 258, 259, 260, 261, 432, 433, 434, 517, 435, 436, 437, 438, 439, 519, 440, 441, 442, 443, 444, 445, 446, 526, 447, 448, 532, 540, 675, 18, 542, 543, 544, 674, 545, 546, 547, 549, 548, 656, 689, 780, 781, 794, 688, 797, 798, 801, 804, 805, 808, 810, 937];
const CONDITION = [166, 167, 168, 169, 170, 171, 172, 173,  175, 176, 177, 178, 179, 180, 181, 182, 183, 186, 187, 188, 189, 190, 191, 198, 201, 202, 247, 354, 426, 352, 405, 353, 378, 423, 355, 383, 357, 356, 384, 403, 358, 359, 361, 362, 364, 367, 430, 368, 369, 371, 425, 373, 374, 375, 385, 376, 370, 521, 522, 515, 516, 509, 510, 520, 511, 512, 513, 514, 538, 633, 610, 611, 612, 613, 614, 625, 626, 620, 621, 622, 628, 648, 615, 616, 617, 618, 619, 623, 624, 660, 627, 661, 629, 630, 631, 632, 665, 664, 662, 663, 634, 635, 636, 637, 638, 639, 640, 641, 642, 643, 644, 645, 775, 758, 759, 760, 764, 766, 767, 769, 761, 770, 773, 772, 774, 768, 763, 765, 757, 762, 771, 776, 882, 883, 884, 885, 887, 888, 889, 890, 891, 892, 893, 894, 895, 896, 897, 898, 899, 901, 902, 903, 904, 905, 906, 907, 908, 910, 911, 912, 913, 941, 942, 943];
const OUTPUT = [29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 54, 56, 57, 58, 59, 60, 61, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 86, 87, 88, 89, 90, 91, 92, 94, 95, 96, 97, 98, 99, 100, 101, 102, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 127, 128, 129, 130, 131, 132, 133, 134, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 154, 155, 156, 157, 158, 159, 193, 194, 195, 196, 197, 199, 200, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 217, 218, 219,  221, 222, 223, 224, 225, 226,  228, 229, 230, 232, 233, 234,  237, 238, 241, 242, 243, 244, 245, 246, 248, 301, 386, 264, 404, 402, 266, 268, 379, 381, 382, 380, 270, 413, 271, 414, 272, 415, 273, 416, 274, 417, 275, 418, 406, 276, 401, 277, 399, 279, 280, 281, 377, 283, 285, 286, 287, 288, 290, 291, 431, 265, 427, 292, 294, 390, 295, 296, 400, 297, 298, 421, 422, 299, 300, 302, 303, 395, 306, 308, 420, 311, 305, 424, 282, 318, 319, 320, 323, 429, 428, 324, 325, 396, 326, 397, 327, 407, 408, 387, 412, 328, 329, 409, 410, 411, 330, 331, 321, 388, 332, 393, 333, 394, 335, 336, 337, 289, 338, 340, 341, 342, 343, 344, 345, 346, 391, 347, 348, 349, 350, 314, 351, 312, 313, 315, 309, 389, 284, 267, 392, 114, 449, 450, 451, 452, 453, 454, 455, 456, 529, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 524, 523, 477, 471, 472, 525, 473, 474, 475, 476, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 527, 494, 495, 496, 497, 518, 498, 530, 499, 500, 501, 502, 503, 504, 505, 528, 506, 507, 508, 531, 533, 534, 535, 536, 537, 539, 541, 676, 677, 678, 74, 161, 550, 681, 568, 593, 607, 570, 650, 659, 671, 551, 553, 552, 579, 554, 555, 557, 678, 669, 558, 559, 560, 647, 561, 658, 672, 562, 563, 653, 649, 652, 670, 564, 565, 556, 566, 567, 682, 683, 569, 572, 571, 573, 574, 575, 576, 655, 577, 578, 680, 580, 581, 582, 673, 677, 583, 584, 585, 586, 587, 588, 679, 589, 590, 591, 651, 592, 594, 595, 596, 597, 598, 599, 646, 676, 667, 600, 604, 601, 602, 657, 603, 654, 605, 606, 668, 608, 666, 609, 690, 692, 693, 694, 695, 696, 698, 700, 701, 702, 703, 704, 706, 710, 707, 708, 709, 713, 714, 715, 716, 718, 724, 725, 726, 728, 729, 730, 731, 732, 733, 734, 736, 737, 739, 756, 740, 741, 742, 743, 744, 745, 746, 747, 752, 753, 755, 790, 791, 738, 691, 717, 720, 721, 722, 750, 751, 748, 749, 697, 719, 727, 735, 712, 711, 723, 754, 705, 699, 777, 778, 779, 684, 685, 686, 687, 782, 783, 784, 785, 786, 787, 788, 789, 792, 793, 795, 796, 799, 811, 812, 813, 814, 815, 816, 817, 818, 819, 820, 821, 822, 823, 824, 825, 826, 827, 828, 830, 831, 832, 833, 835, 836, 837, 838, 839, 840, 841, 843, 844, 845, 846, 847, 848, 849, 850, 851, 852, 853, 854, 855, 856, 857, 858, 859, 860, 862, 863, 865, 866, 867, 868, 869, 870, 871, 872, 873, 874, 875, 876, 877, 878, 880, 881, 916, 918, 919, 920, 921, 922, 923, 924, 925, 926, 927, 928, 929, 930, 931, 932, 933, 934, 936, 938, 939, 940, 914, 103];

const TRIGGER_LASTSET = [797, 798, 801, 804, 805, 808, 810, 937];
const CONDITION_LASTSET = [882, 883, 884, 885, 887, 888, 889, 890, 891, 892, 893, 894, 895, 896, 897, 898, 899, 901, 902, 903, 904, 905, 906, 907, 908, 910, 911, 912, 913, 941, 942, 943];
const OUTPUT_LASTSET = [796, 799, 811, 812, 813, 814, 815, 816, 817, 818, 819, 820, 821, 822, 823, 824, 825, 826, 827, 828, 830, 831, 832, 833, 835, 836, 837, 838, 839, 840, 841, 843, 844, 845, 846, 847, 848, 849, 850, 851, 852, 853, 854, 855, 856, 857, 858, 859, 860, 862, 863, 865, 866, 867, 868, 869, 870, 871, 872, 873, 874, 875, 876, 877, 878, 880, 881, 916, 918, 919, 920, 921, 922, 923, 924, 925, 926, 927, 928, 929, 930, 931, 932, 933, 934, 936, 938, 939, 940];