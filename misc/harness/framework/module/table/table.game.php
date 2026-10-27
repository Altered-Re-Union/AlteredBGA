<?php

/**
 * Local replacement for the proprietary BGA framework (module/table/table.game.php).
 *
 * Only the surface used by the game is implemented. Database calls go to a real
 * MariaDB so that the game's SQL runs unchanged. The state machine follows
 * states.inc.php: entering a state runs its 'action' synchronously, then computes
 * its 'args' if the state waits for a player (as BGA does before notifying clients).
 */

namespace Bga\GameFramework {
  class SystemException extends \Exception {}
  class VisibleSystemException extends \Exception {}
  class UserException extends \Exception {}
}

namespace {

  require_once dirname(__DIR__, 2) . '/rng.php';

  if (!defined('APP_GAMEMODULE_PATH')) {
    define('APP_GAMEMODULE_PATH', dirname(__DIR__, 2) . '/');
  }

  class feException extends \Exception {}
  class BgaSystemException extends \Bga\GameFramework\SystemException {}
  class BgaVisibleSystemException extends \Bga\GameFramework\VisibleSystemException {}
  class BgaUserException extends \Bga\GameFramework\UserException {}

  function clienttranslate($s) { return $s; }
  if (!function_exists('_')) {
    function _($s) { return $s; } // gettext, available on BGA
  }
  function totranslate($s) { return $s; }
  function bga_rand($min, $max) { return \Harness\Rng::randInt($min, $max); }
  function mysql_escape_string($s) { return BgaDb::conn()->real_escape_string((string) $s); }

  /**
   * Connection + instrumentation shared by every DB call.
   */
  class BgaDb
  {
    private static ?\mysqli $conn = null;
    public static int $queryCount = 0;
    public static float $queryTime = 0.0;
    /** @var array<string,int> normalized query => count, filled only when profiling */
    public static ?array $profile = null;

    public static function conn(): \mysqli
    {
      if (self::$conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        self::$conn = new \mysqli(getenv('DB_HOST') ?: '127.0.0.1', 'root', 'root');
        self::$conn->set_charset('utf8mb4');
      }
      return self::$conn;
    }

    public static function query(string $sql)
    {
      self::$queryCount++;
      if (self::$profile !== null) {
        $key = preg_replace(['/\'[^\']*\'/', '/\b\d+\b/'], ['?', 'N'], $sql);
        self::$profile[$key] = (self::$profile[$key] ?? 0) + 1;
      }
      $t = microtime(true);
      try {
        return self::conn()->query($sql);
      } catch (\mysqli_sql_exception $e) {
        throw new \feException('SQL error: ' . $e->getMessage() . "\n" . $sql);
      } finally {
        self::$queryTime += microtime(true) - $t;
      }
    }

    /** Drop and recreate the game database from the framework schema + dbmodel.sql */
    public static function resetDatabase(string $gameDir): void
    {
      $db = self::conn();
      $name = getenv('DB_NAME') ?: 'altered';
      $db->query("DROP DATABASE IF EXISTS `$name`");
      $db->query("CREATE DATABASE `$name`");
      $db->select_db($name);
      $sql = file_get_contents(dirname(__DIR__, 3) . '/schema.sql') . "\n" . file_get_contents($gameDir . '/dbmodel.sql');
      $db->multi_query($sql);
      while ($db->more_results()) {
        $db->next_result();
      }
    }
  }

  class APP_Object
  {
    public static array $logs = [];
    function dump($v, $value) { self::$logs[] = "$v=" . var_export($value, true); }
    function info($value) { self::$logs[] = $value; }
    function trace($value) { self::$logs[] = $value; }
    function debug($value) { self::$logs[] = $value; }
    function watch($value) { self::$logs[] = $value; }
    function warn($value) { self::$logs[] = $value; }
    function error($msg) { self::$logs[] = $msg; }
  }

  class APP_DbObject extends APP_Object
  {
    public static function DbQuery($sql)
    {
      return BgaDb::query($sql);
    }

    public static function DbGetLastId()
    {
      return BgaDb::conn()->insert_id;
    }

    public static function DbAffectedRow()
    {
      return BgaDb::conn()->affected_rows;
    }

    public static function escapeStringForDB($s)
    {
      return BgaDb::conn()->real_escape_string((string) $s);
    }

    public static function getObjectListFromDB($sql, $bUniqueValue = false)
    {
      $res = BgaDb::query($sql);
      $rows = [];
      while ($row = $res->fetch_assoc()) {
        $rows[] = $bUniqueValue ? reset($row) : $row;
      }
      return $rows;
    }

    public static function getCollectionFromDB($sql, $bSingleValue = false)
    {
      $out = [];
      foreach (self::getObjectListFromDB($sql) as $row) {
        $key = reset($row);
        $out[$key] = $bSingleValue ? next($row) : $row;
      }
      return $out;
    }

    public static function getUniqueValueFromDB($sql)
    {
      $rows = self::getObjectListFromDB($sql);
      if (count($rows) > 1) {
        throw new \feException("getUniqueValueFromDB: more than one row\n$sql");
      }
      return empty($rows) ? null : reset($rows[0]);
    }

    public static function getObjectFromDB($sql)
    {
      $rows = self::getObjectListFromDB($sql);
      return $rows[0] ?? null;
    }

    public static function getNonEmptyObjectFromDB($sql)
    {
      $row = self::getObjectFromDB($sql);
      if ($row === null) {
        throw new \feException("getNonEmptyObjectFromDB: empty result\n$sql");
      }
      return $row;
    }
  }

  class APP_GameClass extends APP_DbObject {}

  /**
   * Minimal state machine driven by states.inc.php
   */
  class GameState
  {
    public array $states;
    public ?array $lastArgs = null;
    /** @var int[] ordered list of visited state ids, for assertions */
    public array $history = [];

    public function __construct(private Table $table, array $states)
    {
      $this->states = $states;
    }

    public function state_id(): int
    {
      return (int) Table::getGlobal(1);
    }

    public function state($bSkipStateArgs = false, $bOnlyVariableContent = false, $bSkipReflexionTimeLoad = false)
    {
      $id = $this->state_id();
      $state = $this->states[$id] ?? [];
      $state['id'] = $id;
      if (!$bSkipStateArgs && isset($state['args']) && $this->isPlayerState($state)) {
        $state['args'] = $this->computeArgs($state);
      }
      return $state;
    }

    private function isPlayerState(array $state): bool
    {
      return in_array($state['type'] ?? '', ['activeplayer', 'multipleactiveplayer'], true);
    }

    private function computeArgs(array $state)
    {
      $method = $state['args'];
      return $this->table->$method();
    }

    public function jumpToState($stateId)
    {
      if (!isset($this->states[$stateId])) {
        throw new \feException("jumpToState: unknown state $stateId");
      }
      Table::setGlobal(1, $stateId);
      $this->history[] = (int) $stateId;
      Table::$notifications[] = ['target' => null, 'name' => 'gameStateChange', 'msg' => '', 'args' => ['id' => (int) $stateId, 'active_player' => (int) Table::getGlobal(2)]];
      $state = $this->states[$stateId];
      if (isset($state['updateGameProgression']) && $state['updateGameProgression']) {
        $this->table->getGameProgression();
      }
      if (isset($state['action'])) {
        $method = $state['action'];
        $this->table->$method();
      }
      // BGA computes args once the state is settled, before sending gameStateChange
      if ($this->state_id() == $stateId && $this->isPlayerState($state) && isset($state['args'])) {
        $this->lastArgs = $this->computeArgs($state);
      }
    }

    public function nextState($transition = '')
    {
      $state = $this->states[$this->state_id()];
      $transitions = $state['transitions'] ?? [];
      if (isset($transitions[$transition])) {
        $next = $transitions[$transition];
      } elseif (count($transitions) == 1) {
        $next = reset($transitions);
      } else {
        throw new \feException("nextState: no transition '$transition' from state {$state['name']}");
      }
      $this->jumpToState($next);
    }

    public function checkPossibleAction($action)
    {
      $state = $this->states[$this->state_id()];
      if (isset($state['possibleactions']) && !in_array($action, $state['possibleactions'])) {
        throw new \BgaUserException("Impossible action $action in state {$state['name']}");
      }
      return true;
    }

    public function changeActivePlayer($pId)
    {
      Table::setGlobal(2, $pId);
    }

    public function setAllPlayersMultiactive()
    {
      Table::DbQuery('UPDATE player SET player_is_multiactive = 1 WHERE player_zombie = 0');
    }

    public function setPlayersMultiactive($players, $nextState, $bExclusive = false)
    {
      if ($bExclusive) {
        Table::DbQuery('UPDATE player SET player_is_multiactive = 0');
      }
      if (!empty($players)) {
        Table::DbQuery('UPDATE player SET player_is_multiactive = 1 WHERE player_id IN (' . implode(',', array_map('intval', $players)) . ')');
      }
      if (empty($this->getActivePlayerList())) {
        $this->nextState($nextState);
        return true;
      }
      return false;
    }

    public function setPlayerNonMultiactive($pId, $nextState)
    {
      Table::DbQuery('UPDATE player SET player_is_multiactive = 0 WHERE player_id = ' . (int) $pId);
      if (empty($this->getActivePlayerList())) {
        $this->nextState($nextState);
        return true;
      }
      return false;
    }

    public function getActivePlayerList()
    {
      return array_map('intval', Table::getObjectListFromDB('SELECT player_id FROM player WHERE player_is_multiactive = 1', true));
    }

    public function isPlayerActive($pId)
    {
      $state = $this->states[$this->state_id()];
      if (($state['type'] ?? '') == 'multipleactiveplayer') {
        return in_array((int) $pId, $this->getActivePlayerList());
      }
      return ($state['type'] ?? '') == 'activeplayer' && (int) Table::getGlobal(2) == (int) $pId;
    }
  }

  /** $this->bga of the BGA framework: only the tournament API is used */
  class BgaTournament
  {
    public function isTournament(): bool { return false; }
    public function getInfo(): array { return []; }
    public function getSeedInfo(): array { return []; }
  }

  abstract class Table extends APP_GameClass
  {
    public $table_id = 1;
    public object $bga;
    /** 'studio' or 'prod': some code paths differ (debug dice, end-of-game messages) */
    public static string $environment = 'studio';
    public GameState $gamestate;
    public array $player_preferences = [];
    public $bSelectGlobalsForUpdate = false;
    protected array $gameStateLabels = [];
    protected array $gameinfos;
    protected array $stats_type;

    /** Player whose request is being processed (BGA's "current player") */
    public static ?int $currentPlayerId = null;
    /** @var int[] player ids in seat order; empty = ascending ids */
    public static array $seatOrder = [];
    /** @var array<int, array{target: ?int, name: string, msg: string, args: array}> */
    public static array $notifications = [];

    public function __construct()
    {
      include 'material.inc.php';
      include 'states.inc.php';
      include 'gameinfos.inc.php';
      // BGA reads stats.json when present (stats.inc.php is the legacy format)
      if (file_exists('stats.json')) {
        $stats_type = json_decode(file_get_contents('stats.json'), true);
      } else {
        include 'stats.inc.php';
      }
      $this->gamestate = new GameState($this, $machinestates);
      $this->bga = (object) ['tournament' => new BgaTournament()];
      $this->gameinfos = $gameinfos;
      $this->stats_type = $stats_type;
    }

    abstract protected function getGameName();

    /** BGA loads the global table once per request and keeps it in memory */
    private static ?array $globals = null;

    public static function getGlobal(int $id)
    {
      if (self::$globals === null) {
        self::$globals = self::getCollectionFromDB('SELECT global_id, global_value FROM global', true);
      }
      return self::$globals[$id] ?? null;
    }

    public static function setGlobal(int $id, $value)
    {
      $value = (int) $value;
      self::getGlobal($id);
      self::$globals[$id] = $value;
      self::DbQuery("INSERT INTO global (global_id, global_value) VALUES ($id, $value) ON DUPLICATE KEY UPDATE global_value = $value");
    }

    /** Raw SQL on the global table (e.g. debug tools) must not leave a stale copy */
    public static function harnessResetGlobalsCache(): void
    {
      self::$globals = null;
    }

    public function initGameStateLabels($labels)
    {
      $this->gameStateLabels = array_merge($this->gameStateLabels, $labels);
    }

    public function getGameStateValue($label)
    {
      return (int) self::getGlobal($this->gameStateLabels[$label]);
    }

    public function setGameStateValue($label, $value)
    {
      self::setGlobal($this->gameStateLabels[$label], $value);
    }

    public function setGameStateInitialValue($label, $value)
    {
      $this->setGameStateValue($label, $value);
    }

    public function incGameStateValue($label, $inc)
    {
      $this->setGameStateValue($label, $this->getGameStateValue($label) + $inc);
    }

    public function getGameinfos()
    {
      return $this->gameinfos;
    }

    public function getStatTypes()
    {
      return $this->stats_type;
    }

    public function getBgaEnvironment()
    {
      return self::$environment;
    }

    public function _($s)
    {
      return $s;
    }

    public function getActivePlayerId()
    {
      return self::getGlobal(2);
    }

    public function getActivePlayerName()
    {
      return $this->loadPlayersBasicInfos()[$this->getActivePlayerId()]['player_name'] ?? '';
    }

    public function getCurrentPlayerId($bReturnNullIfNotLogged = false)
    {
      return self::$currentPlayerId ?? (int) $this->getActivePlayerId();
    }

    public function getCurrentPlayerName($bReturnEmptyIfNotLogged = false)
    {
      return $this->loadPlayersBasicInfos()[$this->getCurrentPlayerId()]['player_name'] ?? '';
    }

    public function getPlayersNumber()
    {
      return count($this->loadPlayersBasicInfos());
    }

    public function loadPlayersBasicInfos()
    {
      $order = empty(self::$seatOrder) ? 'player_no, player_id' : 'FIELD(player_id, ' . implode(',', array_map('intval', self::$seatOrder)) . ')';
      return self::getCollectionFromDB(
        "SELECT player_id, player_name, player_color, player_no, player_zombie, player_eliminated FROM player ORDER BY $order"
      );
    }

    public function reloadPlayersBasicInfos() {}

    public function reattributeColorsBasedOnPreferences($players, $colors) {}

    public function getNextPlayerTable()
    {
      $ids = array_map('intval', array_keys($this->loadPlayersBasicInfos()));
      $table = [0 => $ids[0]];
      foreach ($ids as $i => $id) {
        $table[$id] = $ids[($i + 1) % count($ids)];
      }
      return $table;
    }

    public function getPrevPlayerTable()
    {
      $ids = array_map('intval', array_keys($this->loadPlayersBasicInfos()));
      $table = [];
      foreach ($ids as $i => $id) {
        $table[$id] = $ids[($i - 1 + count($ids)) % count($ids)];
      }
      return $table;
    }

    public function activeNextPlayer()
    {
      $next = $this->getNextPlayerTable()[(int) $this->getActivePlayerId()] ?? $this->getNextPlayerTable()[0];
      $this->gamestate->changeActivePlayer($next);
      return $next;
    }

    public function activePrevPlayer()
    {
      $prev = $this->getPrevPlayerTable()[(int) $this->getActivePlayerId()];
      $this->gamestate->changeActivePlayer($prev);
      return $prev;
    }

    public function checkAction($action, $bThrowException = true)
    {
      try {
        return $this->gamestate->checkPossibleAction($action);
      } catch (\BgaUserException $e) {
        if ($bThrowException) {
          throw $e;
        }
        return false;
      }
    }

    public function giveExtraTime($pId, $time = null) {}

    /** @var array<int, array{0: string, 1: array}> calls to external services, see getGenericGameInfos */
    public static array $externalCalls = [];
    /** @var ?callable(string, array): array */
    public static $externalResponder = null;

    /** BGA proxy to the publisher's API (Altered decks, game results): recorded, never sent */
    public static function getGenericGameInfos($method, $args = [])
    {
      self::$externalCalls[] = [$method, $args];
      return self::$externalResponder ? (self::$externalResponder)($method, $args) : ['success' => 1, 'content' => []];
    }

    /** Index in $notifications of the last forced flush (sendNotifications), null if none */
    public static ?int $lastFlush = null;

    /** Sends the pending notifications right away; recorded so a test can tell what came before */
    public function sendNotifications()
    {
      self::$lastFlush = count(self::$notifications);
    }

    public function notifyAllPlayers($name, $msg, $args)
    {
      self::$notifications[] = ['target' => null, 'name' => $name, 'msg' => $msg, 'args' => $args];
    }

    public function notifyPlayer($pId, $name, $msg, $args)
    {
      self::$notifications[] = ['target' => (int) $pId, 'name' => $name, 'msg' => $msg, 'args' => $args];
    }

    /** Framework action of the final state (99): the table is over */
    public function stGameEnd() {}

    public function stGameSetup()
    {
      $this->gamestate->nextState('');
    }

    /**
     * Equivalent of BGA table creation: setupNewGame, then the state machine starts at state 1.
     * @param array<int,string> $players player_id => name, in seat order
     */
    public static function harnessCreate(array $players, array $options = []): static
    {
      self::$seatOrder = array_keys($players);
      $infos = [];
      foreach ($players as $pId => $name) {
        $infos[$pId] = ['player_canal' => 'canal', 'player_name' => $name, 'player_avatar' => '000000', 'beginner' => false];
      }
      // Unspecified options take their default from gameoptions.json (first value if none)
      foreach (json_decode(file_get_contents('gameoptions.json'), true) as $id => $option) {
        $options[$id] ??= $option['default'] ?? (int) array_key_first($option['values']);
      }
      $game = new static();
      $game->setupNewGame($infos, $options);
      $no = 1;
      foreach (array_keys($game->loadPlayersBasicInfos()) as $pId) {
        self::DbQuery("UPDATE player SET player_no = " . $no++ . " WHERE player_id = $pId");
      }
      $game->gamestate->jumpToState(1);
      return $game;
    }
  }
}
