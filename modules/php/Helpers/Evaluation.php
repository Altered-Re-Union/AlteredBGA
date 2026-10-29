<?php

namespace ALT\Helpers;

/**
 * Read-only evaluation scope.
 *
 * Evaluating the engine tree (choices, doability, targets...) asks the same questions many times,
 * and nothing changes the game while it runs. The outermost call opens a scope where results are
 * shared; the scope, and everything kept in it, ends when that call returns.
 */
class Evaluation
{
  protected static ?array $results = null;

  /** Runs $compute once per key within the scope, opening the scope if none is open */
  public static function run(string $key, callable $compute)
  {
    $outermost = self::$results === null;
    if ($outermost) {
      self::$results = [];
    }
    try {
      if (!array_key_exists($key, self::$results)) {
        self::$results[$key] = $compute();
      }
      return self::$results[$key];
    } finally {
      if ($outermost) {
        self::$results = null;
      }
    }
  }

  /** Same as run while a scope is open; outside of one, just computes */
  public static function cached(string $key, callable $compute)
  {
    return self::$results === null ? $compute() : self::run($key, $compute);
  }
}
