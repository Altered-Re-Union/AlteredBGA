<?php

/**
 * Hooks on the game's randomness. The game calls shuffle() unqualified from its own
 * namespaces, so defining shuffle() in those namespaces intercepts it without touching
 * the game code. bga_rand() is a framework function, defined in table.game.php.
 */

namespace Harness {
  class Rng
  {
    /** @var ?callable(array): ?array returns the forced order, or null to shuffle randomly */
    public static $shuffle = null;
    /** @var ?callable(int, int): ?int */
    public static $randInt = null;

    public static function shuffle(array &$a): bool
    {
      if (self::$shuffle !== null && ($forced = (self::$shuffle)($a)) !== null) {
        $a = $forced;
        return true;
      }
      return \shuffle($a);
    }

    public static function randInt(int $min, int $max): int
    {
      if (self::$randInt !== null && ($forced = (self::$randInt)($min, $max)) !== null) {
        return $forced;
      }
      return mt_rand($min, $max);
    }
  }
}

namespace ALT\Helpers {
  function shuffle(array &$a): bool { return \Harness\Rng::shuffle($a); }
}

namespace ALT\Core {
  function shuffle(array &$a): bool { return \Harness\Rng::shuffle($a); }
}

namespace ALT\Managers {
  function shuffle(array &$a): bool { return \Harness\Rng::shuffle($a); }
}
