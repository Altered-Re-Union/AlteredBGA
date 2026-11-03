<?php

namespace ALT\Helpers;

use ALT\Core\Game;

abstract class DB_Model extends \APP_DbObject implements \JsonSerializable
{
  protected $table = null;
  protected $primary = null;
  protected $log = null;
  /**
   * This associative array will link class attributes to db fields
   */
  protected $attributes = [];

  /**
   * This array will contains class attributes that does not depends on the DB (static info), they can only be accessed, not modified
   */
  protected $staticAttributes = [];

  /**
   * Fill in class attributes based on DB entry
   */
  public function __construct($row)
  {
    foreach ($this->attributes as $attribute => $field) {
      $fieldName = is_array($field) ? $field[0] : $field;

      $this->$attribute = $row[$fieldName] ?? null;
      if (is_array($field) && !is_null($this->$attribute)) {
        if ($field[1] == 'int') {
          $this->$attribute = (int) $this->$attribute;
        }
        if ($field[1] == 'bool') {
          $this->$attribute = (bool) $this->$attribute;
        }
        if ($field[1] == 'obj') {
          if (!is_array($this->$attribute)) {
            $this->$attribute = json_decode($this->$attribute, true);
          }
        }
      }
    }
  }

  /**
   * Get the DB primary row according to attributes mapping
   */
  private function getPrimaryFieldValue()
  {
    foreach ($this->attributes as $attribute => $field) {
      $fieldName = is_array($field) ? $field[0] : $field;
      if ($fieldName == $this->primary) {
        return $this->$attribute;
      }
    }
    return null;
  }

  /*
   * Magic method that intercept not defined method and do the appropriate stuff
   */
  /** @var array<string, ?array> magic method name => [prefix, attribute], parsed once per request */
  protected static $parsedMethods = [];

  /**
   * Splits a magic accessor name (getFoo, setFoo, incFoo, isFoo) into [prefix, attribute].
   * Accessors run millions of times per request when collections are filtered: the regex is
   * only applied once per method name.
   */
  protected static function parseMethod($method)
  {
    if (!array_key_exists($method, self::$parsedMethods)) {
      self::$parsedMethods[$method] = preg_match('/^([gs]et|inc|is)([A-Z])(.*)$/', $method, $match)
        ? [$match[1], mb_strtolower($match[2]) . $match[3]]
        : null;
    }
    return self::$parsedMethods[$method];
  }

  public function __call($method, $args)
  {
    if ($parsed = self::parseMethod($method)) {
      [$prefix, $name] = $parsed;
      if (!\array_key_exists($name, $this->attributes)) {
        // Static attribute getters
        if (in_array($prefix, ['get', 'is'])) {
          foreach ($this->staticAttributes as $attr) {
            if (is_array($attr) && $name == $attr[0]) {
              return $this->$name ?? ($attr[1] == 'int' ? 0 : []);
            } elseif ($attr == $name) {
              return $this->$name ?? '';
            }
          }
        }
        throw new \InvalidArgumentException("Attribute {$name} doesn't exist");
      }

      if ($prefix == 'get') {
        if (count($args) > 0 && is_array($this->attributes[$name]) && $this->attributes[$name][1] == 'obj') {
          // Handle json field
          return $this->$name[$args[0]] ?? null;
        } else {
          // Basic getters
          return $this->$name;
        }
      } elseif ($prefix == 'is') {
        // Boolean getter
        return (bool) ($this->$name != 0);
      } elseif ($prefix == 'set') {
        // Setters in DB and update cache
        $value = $args[0];

        // Auto-cast
        $field = $this->attributes[$name];
        $fieldName = is_array($field) ? $field[0] : $field;
        $isObj = false;
        if (is_array($field)) {
          if ($field[1] == 'int') {
            $value = (int) $value;
            if ($value == $this->$name) {
              return; // No modification, abort DB call
            }
          }
          if ($field[1] == 'bool') {
            $value = (bool) $value;
            if ($value == $this->$name) {
              return; // No modification, abort DB call
            }
          }
          if ($field[1] == 'obj') {
            $isObj = true;
            $value = count($args) > 1 ? $args[1] : $args[0];
            $objKey = count($args) > 1 ? $args[0] : null;
          }
        }

        if ($isObj && $objKey !== null) {
          $this->$name[$objKey] = $value;
        } else {
          $this->$name = $value;
        }

        $updateValue = $this->$name;
        if ($isObj) {
          $updateValue = json_encode($updateValue);
        }
        if ($value != null) {
          $updateValue = \addslashes($updateValue);
        }

        // $this->DB()->update([$this->attributes[$name] => \addslashes($value)], $this->getPrimaryFieldValue());
        $this->DB()->update([$fieldName => $updateValue], $this->getPrimaryFieldValue());
        return $value;
      } elseif ($prefix == 'inc') {
        $getter = 'get' . ucfirst($name);
        $setter = 'set' . ucfirst($name);
        return $this->$setter($this->$getter() + (empty($args) ? 1 : $args[0]));
      }
    } else {
      throw new \feException('Undefined method ' . $method);
      return null;
    }
  }

  /**
   * Return an array of attributes
   */
  public function jsonSerialize()
  {
    $data = [];
    foreach ($this->attributes as $attribute => $field) {
      $data[$attribute] = $this->$attribute;
    }

    return $data;
  }

  public function getStaticData()
  {
    $data = [];
    foreach ($this->staticAttributes as $attribute) {
      if (is_array($attribute)) {
        $attribute = $attribute[0];
      }
      $getter = 'get' . ucfirst($attribute);
      $data[$attribute] = $this->$getter();
    }

    return $data;
  }

  public function getUiData()
  {
    return array_merge($this->jsonSerialize(), $this->getStaticData());
  }

  /**
   * Private DB call
   */
  private function DB()
  {
    if (is_null($this->table)) {
      throw new \feException('You must specify the table you want to do the query on');
    }

    $log = null;

    if (static::$log ?? Game::get()->getGameStateValue('logging') == 1) {
      $log = new Log($this->table, $this->primary);
    }

    return new QueryBuilder(
      $this->table,
      function ($row) {
        return $row;
      },
      $this->primary,
      $log
    );
  }
}
