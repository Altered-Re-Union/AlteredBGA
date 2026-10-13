<?php

namespace ALT\Helpers;

abstract class Utils extends \APP_DbObject
{
  public static function SEQ(...$childs)
  {
    return [
      'type' => NODE_SEQ,
      'childs' => $childs,
    ];
  }
  public static function OR(...$childs)
  {
    return [
      'type' => NODE_OR,
      'childs' => $childs,
    ];
  }
  public static function PAR(...$childs)
  {
    return [
      'type' => NODE_PARALLEL,
      'childs' => $childs,
    ];
  }
  public static function ACTION($actionName, $args, $node = [])
  {
    $node['action'] = $actionName;
    $node['args'] = $args;
    return $node;
  }
  public static function GAIN($token, $n = 1)
  {
    return self::ACTION(GAIN, ['type' => $token, 'n' => $n]);
  }

  public static function filter(&$data, $filter)
  {
    $data = array_values(array_filter($data, $filter));
  }

  public static function rand($array, $n = 1)
  {
    $keys = array_rand($array, $n);
    if ($n == 1) {
      $keys = [$keys];
    }
    $entries = [];
    foreach ($keys as $key) {
      $entries[] = $array[$key];
    }
    shuffle($entries);
    return $entries;
  }

  public static function search($array, $test)
  {
    $found = false;
    $iterator = new \ArrayIterator($array);

    while ($found === false && $iterator->valid()) {
      if ($test($iterator->current())) {
        $found = $iterator->key();
      }
      $iterator->next();
    }

    return $found;
  }

  public static function topological_sort($nodeids, $edges)
  {
    $L = $S = $nodes = [];
    foreach ($nodeids as $id) {
      $nodes[$id] = ['in' => [], 'out' => []];
      foreach ($edges as $e) {
        if ($id == $e[0]) {
          $nodes[$id]['out'][] = $e[1];
        }
        if ($id == $e[1]) {
          $nodes[$id]['in'][] = $e[0];
        }
      }
    }
    foreach ($nodes as $id => $n) {
      if (empty($n['in'])) {
        $S[] = $id;
      }
    }
    while (!empty($S)) {
      $L[] = $id = array_shift($S);
      foreach ($nodes[$id]['out'] as $m) {
        $nodes[$m]['in'] = array_diff($nodes[$m]['in'], [$id]);
        if (empty($nodes[$m]['in'])) {
          $S[] = $m;
        }
      }
      $nodes[$id]['out'] = [];
    }
    foreach ($nodes as $n) {
      if (!empty($n['in']) or !empty($n['out'])) {
        return null; // not sortable as graph is cyclic
      }
    }
    return $L;
  }

  public static function die($args = null)
  {
    throw new \Bga\GameFramework\VisibleSystemException(json_encode($args));
  }

  /**
   * Return a string corresponding to an assoc array of resources
   */
  public static function resourcesToStr($resources, $keepZero = false)
  {
    $descs = [];
    foreach ($resources as $resource => $amount) {
      if (in_array($resource, ['sources', 'sourcesDesc', 'cardId', 'cId', 'pId', 'income'])) {
        continue;
      }

      if ($amount == 0 && !$keepZero) {
        continue;
      }

      if (in_array($resource, [])) {
        $descs[] = '<' . strtoupper($resource) . ':' . $amount . '>';
      } else {
        $descs[] = $amount . '<' . strtoupper($resource) . '>';
      }
    }
    return implode(',', $descs);
  }

  public static function tagTree($t, $tags, $replaceOnly = false)
  {
    foreach ($tags as $tag => $v) {
      if (!$replaceOnly || ($replaceOnly && isset($t[$tag]))) {
        // if (is_array($t)) {
        $t[$tag] = $v;
        // }
      }
    }

    if (isset($t['childs'])) {
      $t['childs'] = array_map(function ($child) use ($tags, $replaceOnly) {
        return self::tagTree($child, $tags, $replaceOnly);
      }, $t['childs']);
    }
    if (isset($t['args']['effect']) && is_array($t['args']['effect'])) {
      $t['args']['effect'] = self::tagTree($t['args']['effect'], $tags, $replaceOnly);
    }
    if (isset($t['args']['oppositeEffect']) && is_array($t['args']['oppositeEffect'])) {
      $t['args']['oppositeEffect'] = self::tagTree($t['args']['oppositeEffect'], $tags, $replaceOnly);
    }
    return $t;
  }

  /**
   * Pin the passive owner's card id on ACTIVATE_EFFECT nodes with ownEffect.
   * Prevents resolving the wrong card via getSource() when the listener event
   * references another card (e.g. effect 840 after someone else's {D} activation).
   */
  public static function bindOwnEffectActivateCardId($t, $cardId)
  {
    if (!is_array($t)) {
      return $t;
    }

    // Support + ownEffect (e.g. output 840): activate my {D}. Reserve + ownEffect (Thomas Edison,
    // output 705) activates another card's {R} and binds the target via Target::updateCardId.
    if (
      ($t['action'] ?? '') === ACTIVATE_EFFECT
      && ($t['args']['ownEffect'] ?? false)
      && ($t['args']['effectType'] ?? '') === 'Support'
    ) {
      $t['args']['cardId'] = $cardId;
    }

    if (isset($t['childs'])) {
      $t['childs'] = array_map(function ($child) use ($cardId) {
        return self::bindOwnEffectActivateCardId($child, $cardId);
      }, $t['childs']);
    }
    foreach (['effect', 'oppositeEffect'] as $key) {
      if (isset($t['args'][$key]) && is_array($t['args'][$key])) {
        $t['args'][$key] = self::bindOwnEffectActivateCardId($t['args'][$key], $cardId);
      }
    }

    return $t;
  }

  public static function tagPId($t, $pId)
  {
    if (!isset($t['pId'])) {
      $t['pId'] = $pId;
    }

    if (isset($t['childs'])) {
      $t['childs'] = array_map(function ($child) use ($pId) {
        return self::tagPId($child, $pId);
      }, $t['childs']);
    }
    return $t;
  }

  public static function updateTree($t, $searched, $newValue, $limitedKeys = [])
  {
    foreach ($t as $key => $value) {
      if ($limitedKeys == [] && $value === $searched) {
        $t[$key] = $newValue;
      } elseif (in_array($key, $limitedKeys)  && $value === $searched) {
        $t[$key] = $newValue;
      } elseif (is_array($value)) {
        $t[$key] = self::updateTree($value, $searched, $newValue, $limitedKeys);
      }
    }

    return $t;
  }

  public static function searchTree($t, $searched)
  {
    foreach ($t as $key => $value) {
      if ($value === $searched) {
        return true;
      } elseif (is_array($value)) {
        if (self::searchTree($value, $searched)) {
          return true;
        }
      }
    }

    return false;
  }

  public static function formatFee($cost)
  {
    return [
      'fees' => [$cost],
    ];
  }

  public static function uniqueZones($arr1)
  {
    return array_values(
      array_uunique($arr1, function ($a, $b) {
        return $a['x'] == $b['x'] ? $a['y'] - $b['y'] : $a['x'] - $b['x'];
      })
    );
  }

  /**
   * Intersect two arrays of obj with keys x,y
   */
  public static function intersectZones($arr1, $arr2)
  {
    return array_values(
      \array_uintersect($arr1, $arr2, function ($a, $b) {
        return $a['x'] == $b['x'] ? $a['y'] - $b['y'] : $a['x'] - $b['x'];
      })
    );
  }

  /**
   * Diff two arrays of obj with keys x,y
   */
  public static function diffZones($arr1, $arr2)
  {
    return array_values(
      array_udiff($arr1, $arr2, function ($a, $b) {
        return $a['x'] == $b['x'] ? $a['y'] - $b['y'] : $a['x'] - $b['x'];
      })
    );
  }

  public static function bonus_diff($array1, $array2)
  {
    $result = [];
    foreach ($array1 as $key => $val) {
      if (!in_array($val, $array2)) {
        $result[] = $val;
      }
    }

    return $result;
  }

  public static function cartesian($input)
  {
    $result = [[]];

    foreach ($input as $key => $values) {
      $append = [];
      $values = array_unique($values, SORT_NUMERIC);

      foreach ($result as $product) {
        foreach ($values as $item) {
          $product[$key] = $item;
          $append[] = $product;
        }
      }

      $result = $append;
    }

    return $result;
  }

  public static function convertFaction($faction)
  {
    switch ($faction) {
      case 'AX':
        return FACTION_AX;
        break;
      case 'OR':
        return FACTION_OD;
        break;
      case 'BR':
        return FACTION_BR;
        break;
      case 'LY':
        return FACTION_LY;
        break;
      case 'MU':
        return FACTION_MU;
        break;
      case 'YZ':
        return FACTION_YZ;
        break;
    }
  }

  public static function checkAttributeCondition($attribute, $data, $player, $card, $location = null)
  {
    $attributeData = explode(':', $data);
    if (count($attributeData) == 1) {
      return $attributeData[0];
    } else {
      $condArgs = array_slice($attributeData, 1);
      // there is a condition after
      if (Conditions::check(['condition' => implode(':', $condArgs)], $card, ['location' => $location])) {
        return $attributeData[0];
      }
      return  null;
    }
  }

  
  public static function costReductionBucketsOverlap($typeA, $typeB)
  {
    if ($typeA == ALL || $typeB == ALL) {
      return true;
    }
    if ($typeA == $typeB) {
      return true;
    }

    foreach (self::costReductionOverlapProbeCards() as $cardTypes) {
      if (
        self::costReductionBucketAppliesToCard($typeA, $cardTypes)
        && self::costReductionBucketAppliesToCard($typeB, $cardTypes)
      ) {
        return true;
      }
    }

    return false;
  }

  private static function costReductionBucketAppliesToCard($bucketType, $card)
  {
    if ($bucketType == ALL) {
      return true;
    }
    if ($bucketType == $card['type']) {
      return true;
    }
    if (in_array($bucketType, $card['additionalTypes'] ?? [], true)) {
      return true;
    }
    if (in_array($bucketType, $card['subtypes'] ?? [], true)) {
      return true;
    }

    return false;
  }

  private static function costReductionOverlapProbeCards()
  {
    return [
      ['type' => CHARACTER, 'additionalTypes' => [], 'subtypes' => [ARTIST]],
      ['type' => CHARACTER, 'additionalTypes' => [], 'subtypes' => [ANIMAL, SPIRIT]],
      ['type' => CHARACTER, 'additionalTypes' => [], 'subtypes' => [BUREAUCRAT]],
      ['type' => CHARACTER, 'additionalTypes' => [], 'subtypes' => [APPRENTICE, MAGE]],
      ['type' => SPELL, 'additionalTypes' => [], 'subtypes' => [SONG]],
      ['type' => PERMANENT, 'additionalTypes' => [], 'subtypes' => [PLANT]],
      ['type' => PERMANENT, 'additionalTypes' => [], 'subtypes' => [ROBOT]],
      ['type' => TOKEN, 'additionalTypes' => [], 'subtypes' => []],
      ['type' => CHARACTER, 'additionalTypes' => [FEAT], 'subtypes' => []],
    ];
  }
}

function array_uunique($array, $comparator)
{
  $unique_array = [];
  do {
    $element = array_shift($array);
    $unique_array[] = $element;

    $array = array_udiff($array, [$element], $comparator);
  } while (count($array) > 0);

  return $unique_array;
}
