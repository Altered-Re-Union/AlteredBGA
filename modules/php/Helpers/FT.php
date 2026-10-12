<?php

namespace ALT\Helpers;

// Flow Tree
abstract class FT
{
  public static function SEQ(...$childs)
  {
    return [
      'type' => NODE_SEQ,
      'childs' => $childs,
    ];
  }

  public static function SEQ_OPTIONAL(...$childs)
  {
    return [
      'type' => NODE_SEQ,
      'optional' => true,
      'childs' => $childs,
    ];
  }

  public static function SEQ_OPTIONAL_MANUAL(...$childs)
  {
    return [
      'type' => NODE_SEQ,
      'optional' => true,
      'manualChoice' => true,
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

  public static function XOR(...$childs)
  {
    return [
      'type' => NODE_XOR,
      'childs' => $childs,
    ];
  }

  public static function XOR_OPTIONAL(...$childs)
  {
    return [
      'type' => NODE_XOR,
      'childs' => $childs,
      'optional' => true,
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

  public static function GAIN($card, $token, $n = 1, $upTo = 99, $allowDiscarded = false)
  {
    if (!($card instanceof \ALT\Models\Card)) {
      $cardId = $card;
    } else {
      $cardId = $card->getId() ?? ME;
    }
    $args = ['cardId' => $cardId, 'type' => $token, 'n' => $n, 'upTo' => $upTo, 'allowDiscarded' => $allowDiscarded];
    return self::ACTION(GAIN, $args);
  }

  public static function AUGMENT($card)
  {
    if (!($card instanceof \ALT\Models\Card)) {
      $cardId = $card;
    } else {
      $cardId = $card->getId() ?? ME;
    }
    return self::ACTION(GAIN, ['cardId' => $cardId, 'augment' => true]);
  }

  public static function LOOSE($card, $token, $n = 1)
  {
    if (!($card instanceof \ALT\Models\Card)) {
      $cardId = $card;
    } else {
      $cardId = $card->getId() ?? ME;
    }
    return self::ACTION(LOOSE, ['cardId' => $cardId, 'type' => $token, 'n' => $n]);
  }

  public static function RETURN_TO_HAND()
  {
    return self::ACTION(DISCARD, ['destination' => HAND]);
  }

  public static function DISCARD_TO_RESERVE()
  {
    return self::ACTION(DISCARD, ['destination' => RESERVE]);
  }

  public static function RUSH()
  {
    return FT::ACTION(CHOOSE_ASSIGNMENT, ['actions' => ['play'], 'mandatory' => true]);
  }

  public static function RUSH_OPTIONAL()
  {
    return FT::ACTION(CHOOSE_ASSIGNMENT, ['actions' => ['play'], 'mandatory' => true], ['optional' => true]);
  }

  public static function RUSH_CHARACTER()
  {
    return FT::ACTION(CHOOSE_ASSIGNMENT, ['actions' => ['play'], 'mandatory' => true, 'types' => [CHARACTER]]);
  }

  public static function SABOTAGE()
  {
    return  FT::ACTION(TARGET, [
      'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
      'targetLocation' => [RESERVE],
      'upTo' => true,
      'effect' => FT::ACTION(DISCARD, ['desc' => 'sabotage']),
    ]);
  }
}
