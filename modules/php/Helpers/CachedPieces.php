<?php

namespace ALT\Helpers;

/*
 * This is a generic class to manage game pieces.
 *
 * On DB side this is based on a standard table with the following fields:
 * %prefix_%id (string), %prefix_%location (string), %prefix_%state (int)
 *
 *
 * CREATE TABLE IF NOT EXISTS `token` (
 * `token_id` varchar(32) NOT NULL,
 * `token_location` varchar(32) NOT NULL,
 * `token_state` int(10),
 * PRIMARY KEY (`token_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 *
 * CREATE TABLE IF NOT EXISTS `card` (
 * `card_id` int(32) NOT NULL AUTO_INCREMENT,,
 * `card_location` varchar(32) NOT NULL,
 * `card_state` int(10),
 * PRIMARY KEY (`card_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 *
 */

class CachedPieces extends DB_Manager
{
  protected static $table = null;
  protected static $cast = null;

  protected static $prefix = 'piece_';
  protected static $autoIncrement = true;
  protected static $primary;
  protected static $autoremovePrefix = true;
  protected static $autoreshuffle = false; // If true, a new deck is automatically formed with a reshuffled discard as soon at is needed
  protected static $autoreshuffleListener = null; // Callback to a method called when an autoreshuffle occurs
  // autoreshuffleListener = array( 'obj' => object, 'method' => method_name )
  // If defined, tell the name of the deck and what is the corresponding discard (ex : "mydeck" => "mydiscard")
  protected static $autoreshuffleCustom = [];
  protected static $customFields = [];
  protected static $gIndex = [];

  protected static $datas = null;

  public static function DB($table = null)
  {
    static::$primary = static::$prefix . 'id';
    return parent::DB(static::$table);
  }

  /****
   * Return the basic select query fetching basic fields and custom fields
   */
  final static function getSelectQuery()
  {
    $basic = [
      'id' => static::$prefix . 'id',
      'location' => static::$prefix . 'location',
      'state' => static::$prefix . 'state',
    ];
    if (!static::$autoremovePrefix) {
      $basic = array_values($basic);
    }

    $query = self::DB()->select(array_merge($basic, static::$customFields));
    return $query;
  }

  public static function fetchIfNeeded()
  {
    if (is_null(static::$datas)) {
      static::$datas = static::getSelectQuery()->get();
    }
  }

  public static function invalidate()
  {
    static::$datas = null;
  }

  /************************************
   *************************************
   ********* QUERY BUILDER *************
   *************************************
   ************************************/

  public static function where($field, $value)
  {
    return self::getAll()->where($field, $value);
  }

  /****
   * Append the basic select query with a where clause
   */
  public static function getSelectWhere($id = null, $location = null, $state = null)
  {
    return self::where('id', $id)
      ->where('location', $location)
      ->where('state', $state);
  }

  /**
   * Get all the pieces
   */
  public static function getAll()
  {
    static::fetchIfNeeded();
    return static::$datas;
  }

  /************************************
   *************************************
   ********* SANITY CHECKS *************
   *************************************
   ************************************/

  /*
   * Check that the location only contains alphanum and underscore character
   *  -> if the location is an array, implode it using underscores
   */
  final static function checkLocation(&$location, $like = false)
  {
    if (is_null($location)) {
      throw new \Bga\GameFramework\VisibleSystemException('Class Pieces: location cannot be null');
    }

    if (is_array($location)) {
      $location = implode('-', $location);
    }

    $extra = $like ? '%' : '';
    if (preg_match("/^[A-Za-z0-9${extra}-][A-Za-z_0-9${extra}-]*$/", $location) == 0) {
      throw new \Bga\GameFramework\VisibleSystemException("Class Pieces: location must be alphanum and underscore non empty string '$location'");
    }
  }

  /*
   * Check that the id is alphanum and underscore
   */
  final static function checkId(&$id, $like = false)
  {
    if (is_null($id)) {
      throw new \Bga\GameFramework\VisibleSystemException('Class Pieces: id cannot be null');
    }

    $extra = $like ? '%' : '';
    if (preg_match("/^[A-Za-z_0-9${extra}]+$/", $id) == 0) {
      throw new \Bga\GameFramework\VisibleSystemException("Class Pieces: id must be alphanum and underscore non empty string '$id'");
    }
  }

  final static function checkIdArray($arr)
  {
    if (is_null($arr)) {
      throw new \Bga\GameFramework\VisibleSystemException('Class Pieces: tokens cannot be null');
    }

    if (!is_array($arr)) {
      throw new \Bga\GameFramework\VisibleSystemException('Class Pieces: tokens must be an array');
      foreach ($arr as $id) {
        self::checkId($id);
      }
    }
  }

  /*
   * Check that the state is an integer
   */
  final static function checkState($state, $canBeNull = false)
  {
    if (is_null($state) && !$canBeNull) {
      throw new \Bga\GameFramework\VisibleSystemException('Class Pieces: state cannot be null');
    }

    if (!is_null($state) && preg_match('/^-*[0-9]+$/', $state) == 0) {
      throw new \Bga\GameFramework\VisibleSystemException('Class Pieces: state must be integer number');
    }
  }

  /*
   * Check that a given variable is a positive integer
   */
  final static function checkPosInt($n)
  {
    if ($n && preg_match('/^[0-9]+$/', $n) == 0) {
      throw new \Bga\GameFramework\VisibleSystemException('Class Pieces: number of pieces must be integer number');
    }
  }

  /************************************
   *************************************
   ************** GETTERS **************
   *************************************
   ************************************/

  /**
   * Get specific piece by id
   */
  public static function get($id, $raiseExceptionIfNotEnough = true)
  {
    $result = self::getMany($id, $raiseExceptionIfNotEnough);
    return $result->count() == 1 ? $result->first() : $result;
  }

  public static function getMany($ids, $raiseExceptionIfNotEnough = true)
  {
    if (!is_array($ids)) {
      $ids = [$ids];
    }

    // A caller can pass duplicated ids when several effects chain in one atomic
    // resolution. We only need to fetch each piece once from cache.
    $ids = array_values(array_unique(array_map(fn($id) => (string) $id, $ids)));

    self::checkIdArray($ids);
    static::fetchIfNeeded();
    $result = new Collection([]);
    foreach ($ids as $id) {
      if (static::$datas->has($id)) {
        $result[$id] = static::$datas[$id];
      }
    }

    if (count($result) != count($ids) && $raiseExceptionIfNotEnough) {
      $missingIds = array_values(array_diff($ids, array_keys($result->toArray())));
      throw new \feException('Class Pieces: getMany, some pieces have not been found ! missing=' . json_encode($missingIds) . ' asked=' . json_encode($ids));
    }

    return $result;
  }

  public static function getSingle($id, $raiseExceptionIfNotEnough = true)
  {
    $result = self::getMany([$id], $raiseExceptionIfNotEnough);
    return $result->count() == 1 ? $result->first() : null;
  }

  /**
   * Get max or min state of the specific location
   */
  public static function getExtremePosition($getMax, $location)
  {
    $states = self::getInLocation($location)
      ->map(function ($obj) {
        return $obj->getState();
      })
      ->toArray();
    return empty($states) ? 0 : ($getMax ? max($states) : min($states));
  }

  /**
   * Return "$nbr" piece on top of this location, top defined as item with higher state value
   */
  public static function getTopOf($location, $n = 1)
  {
    self::checkLocation($location);
    self::checkPosInt($n);
    return self::getInLocation($location)
      ->orderBy('state', 'DESC')
      ->limit($n);
  }

  /**
   * Return all pieces in specific location
   * note: if "order by" is used, result object is NOT indexed by ids
   */
  public static function getInLocation($location, $state = null)
  {
    self::checkLocation($location, true);
    self::checkState($state, true);
    return self::getSelectWhere(null, $location, $state);
  }

  public static function getInLocationOrdered($location, $state = null)
  {
    return self::getInLocation($location, $state)->orderBy('state', 'ASC');
  }

  /**
   * Return number of pieces in specific location
   */
  public static function countInLocation($location, $state = null)
  {
    self::checkLocation($location, true);
    self::checkState($state, true);
    return self::getInLocation($location, $state)->count();
  }

  /**
   * getFilteredQuery : many times the DB scheme has a pId and a type extra field, this allow for a shortcut for a query for these case
   */
  public static function getFiltered($pId, $location = null, $type = null)
  {
    return self::getSelectWhere(null, $location, null)
      ->where('pId', $pId)
      ->where('type', $type);
  }

  /************************************
   *************************************
   ************** SETTERS **************
   *************************************
   ************************************/
  public static function setState($id, $state)
  {
    self::checkState($state);
    self::checkId($id);
    return self::getSingle($id)->setState($state);
  }

  /*
   * Move one (or many) pieces to given location
   */
  public static function move($ids, $location, $state = 0)
  {
    if (!is_array($ids)) {
      $ids = [$ids];
    }
    if (empty($ids)) {
      return [];
    }

    self::checkLocation($location);
    self::checkState($state);
    self::checkIdArray($ids);
    return self::getMany($ids)
      ->update('location', $location)
      ->update('state', $state);
  }

  /*
   *  Move all tokens from a location to another
   *  !!! state is reset to 0 or specified value !!!
   *  if "fromLocation" and "fromState" are null: move ALL cards to specific location
   */
  public static function moveAllInLocation($fromLocation, $toLocation, $fromState = null, $toState = 0)
  {
    if (!is_null($fromLocation)) {
      self::checkLocation($fromLocation);
    }
    self::checkLocation($toLocation);
    $cards = self::getInLocation($fromLocation, $fromState);

    if (count(explode('-', $toLocation)) > 1) {
      $cards = $cards->where('pId', explode('-', $toLocation)[1]);
    }

    return $cards->update('location', $toLocation)->update('state', $toState);
  }

  /**
   * Move all pieces from a location to another location arg stays with the same value
   */
  public static function moveAllInLocationKeepState($fromLocation, $toLocation)
  {
    self::checkLocation($fromLocation);
    self::checkLocation($toLocation);
    return self::moveAllInLocation($fromLocation, $toLocation, null, null);
  }

  /*
   * Pick the first "$nbr" pieces on top of specified deck and place it in target location
   * Return pieces infos or void array if no card in the specified location
   */
  public static function pickForLocation($nbr, $fromLocation, $toLocation, $state = 0, $deckReform = true)
  {
    self::checkLocation($fromLocation);
    self::checkLocation($toLocation);
    $pieces = self::getTopOf($fromLocation, $nbr);
    $ids = $pieces->getIds();
    $pieces = self::getMany($ids)
      ->update('location', $toLocation)
      ->update('state', $state);

    $genericFromLocation = explode('-', $fromLocation)[0];

    // No more pieces in deck & reshuffle is active => form another deck
    if (
      (array_key_exists($fromLocation, static::$autoreshuffleCustom) ||
        array_key_exists($genericFromLocation, static::$autoreshuffleCustom)) &&
      count($pieces) < $nbr &&
      static::$autoreshuffle &&
      $deckReform
    ) {
      $missing = $nbr - count($pieces);
      self::reformDeckFromDiscard($fromLocation);
      $pieces = $pieces->merge(self::pickForLocation($missing, $fromLocation, $toLocation, $state, false)); // Note: block another deck reform
    }

    return $pieces;
  }

  public static function pickOneForLocation($fromLocation, $toLocation, $state = 0, $deckReform = true)
  {
    return self::pickForLocation(1, $fromLocation, $toLocation, $state, $deckReform)->first();
  }

  /*
   * Reform a location from another location when enmpty
   */
  public static function reformDeckFromDiscard($fromLocation)
  {
    $genericFromLocation = explode('-', $fromLocation);
    $locationShuffle = $genericFromLocation[0];
    self::checkLocation($fromLocation);
    if (!array_key_exists($locationShuffle, static::$autoreshuffleCustom)) {
      throw new \Bga\GameFramework\VisibleSystemException("Class Pieces:reformDeckFromDiscard: Unknown discard location for $locationShuffle !");
    }

    $discard = static::$autoreshuffleCustom[$locationShuffle];
    self::checkLocation($discard);
    self::moveAllInLocation($discard, $fromLocation);
    self::shuffle($fromLocation);
    if (static::$autoreshuffleListener) {
      $obj = new (static::$autoreshuffleListener['obj'])();
      $method = static::$autoreshuffleListener['method'];
      $obj->$method($fromLocation);
    }
  }

  /*
   * Shuffle pieces of a specified location, result of the operation will changes state of the piece to be a position after shuffling
   */
  public static function shuffle($location)
  {
    self::checkLocation($location);
    $pieces = self::getInLocation($location)->getIds();
    shuffle($pieces);
    foreach ($pieces as $state => $id) {
      self::getSingle($id)->setState($state);
    }
  }

  public static function insertOnTop($id, $location)
  {
    $pos = self::getExtremePosition(true, $location);
    self::move($id, $location, $pos + 1);
  }

  public static function insertAtBottom($id, $location)
  {
    $pos = self::getExtremePosition(false, $location);
    self::move($id, $location, $pos - 1);
  }

  /**
   * 241815 - X Marks the Spots fix
   * Place a piece at the Nth position from the top of an ordered location.
   * Top = highest state. Other pieces keep their relative order.
   */
  public static function moveAtPosition($id, $location, $position)
  {
    // Order top-first so position 1 is the current top of deck
    $pieces = self::getInLocation($location)->orderBy('state', 'DESC');

    $ordered = [];
    foreach ($pieces as $pieceId => $_piece) {
      if ($pieceId != $id) {
        $ordered[] = $pieceId;
      }
    }

    $position = max(1, min((int) $position, count($ordered) + 1));
    array_splice($ordered, $position - 1, 0, [$id]);

    // Reassign states: top keeps the highest value
    $pos = count($ordered);
    foreach ($ordered as $pieceId) {
      self::getSingle($pieceId)->setState($pos);
      $pos--;
    }
  }

  /*********************************
   ******** DELETE PIECES **********
   ********************************/
  public static function delete($ids)
  {
    if (!is_array($ids)) {
      $ids = [$ids];
    }
    if (empty($ids)) {
      return [];
    }

    self::checkIdArray($ids);
    $pieces = self::getMany($ids);
    self::DB()
      ->delete()
      ->whereIn(static::$prefix . 'id', $ids)
      ->run();
    foreach ($ids as $id) {
      unset(static::$datas[$id]);
    }

    return $pieces;
  }

  /************************************
   ******** CREATE NEW PIECES **********
   ************************************/

  /* This inserts new records in the database.
   * Generically speaking you should only be calling during setup
   *  with some rare exceptions.
   *
   * Pieces is an array with at least the following fields:
   * [
   *   [
   *     "id" => <unique id>    // This unique alphanum and underscore id, use {INDEX} to replace with index if 'nbr' > 1, i..e "meeple_{INDEX}_red"
   *     "nbr" => <nbr>           // Number of tokens with this id, optional default is 1. If nbr >1 and id does not have {INDEX} it will throw an exception
   *     "nbrStart" => <nbr>           // Optional, if the indexing does not start at 0
   *     "location" => <location>       // Optional argument specifies the location, alphanum and underscore
   *     "state" => <state>             // Optional argument specifies integer state, if not specified and $token_state_global is not specified auto-increment is used
   */

  public static function create($pieces, $globalLocation = null, $globalState = null, $globalId = null)
  {
    $pos = is_null($globalLocation) ? 0 : self::getExtremePosition(true, $globalLocation) + 1;

    $values = [];
    $ids = [];
    foreach ($pieces as $info) {
      $n = $info['nbr'] ?? 1;
      $start = $info['nbrStart'] ?? 0;
      $id = $info['id'] ?? $globalId;
      $location = $info['location'] ?? $globalLocation;
      $state = $info['state'] ?? $globalState;
      if (is_null($state)) {
        $state = $location == $globalLocation ? $pos++ : 0;
      }

      // SANITY
      if (is_null($id) && !static::$autoIncrement) {
        throw new \Bga\GameFramework\VisibleSystemException('Class Pieces: create: id cannot be null if not autoincrement');
      }

      if (is_null($location)) {
        throw new \Bga\GameFramework\VisibleSystemException(
          'Class Pieces : create location cannot be null (set per token location or location_global'
        );
      }
      self::checkLocation($location);

      for ($i = $start; $i < $n + $start; $i++) {
        $data = [];
        if (static::$autoIncrement) {
          $data = [$location, $state];
        } else {
          $nId = preg_replace('/\{INDEX\}/', $id == $globalId ? count($ids) : $i, $id);
          self::checkId($nId);
          $data = [$nId, $location, $state];
          $ids[] = $nId;
        }

        foreach (static::$customFields as $field) {
          if (isset($info[$field]) && is_array($info[$field])) {
            $data[] = \json_encode($info[$field], JSON_UNESCAPED_SLASHES);
          } else {
            $data[] = $info[$field] ?? null;
          }
        }

        $values[] = $data;
      }
    }

    if (empty($values)) {
      return new Collection([]);
    }

    $p = static::$prefix;
    $fields = static::$autoIncrement ? [$p . 'location', $p . 'state'] : [$p . 'id', $p . 'location', $p . 'state'];
    foreach (static::$customFields as $field) {
      $fields[] = $field;
    }

    // With auto increment, we compute the set of all consecutive ids
    self::fetchIfNeeded();
    $ids = self::DB()
      ->multipleInsert($fields)
      ->values($values);

    foreach (
      static::getSelectQuery()
        ->whereIn(static::$prefix . 'id', $ids)
        ->get()
      as $id => $obj
    ) {
      static::$datas[$id] = $obj;
    }

    return self::getMany($ids);
  }

  /*
   * Create a single token
   */
  public static function singleCreate($token)
  {
    return self::create([$token])->first();
  }
}
