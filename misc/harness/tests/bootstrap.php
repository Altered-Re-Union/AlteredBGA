<?php
// Game code uses relative includes (material.inc.php, modules/php/constants.inc.php)
$gameDir = getenv('GAME_DIR') ?: dirname(__DIR__, 3);
chdir($gameDir);
define('GAME_DIR', $gameDir);
define('APP_GAMEMODULE_PATH', dirname(__DIR__) . '/framework/');
require_once $gameDir . '/modules/php/constants.inc.php';
require_once $gameDir . '/altered.game.php';
require_once __DIR__ . '/AlteredTestCase.php';
