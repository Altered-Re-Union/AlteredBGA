<?php

namespace ALT\Cards\NE;

/**
 * Mana Convergence (TOKEN_MANA): a deck card that does nothing. It can be put in Mana, like any card,
 * but never played: no cost, no effect, and no legal zone to play it to, paid or free.
 * Typed SPELL only so the client renders it (there is no card template for its real type).
 */
class NE_Common_ManaConvergence extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_NE_01_C',
      // The only Mana Convergence illustration shipped.
      'asset'  => 'ALT_WCS26_P_NE_01_C',

      'faction'  => FACTION_NE,
      'rarity'  => RARITY_COMMON,
      'name'  => clienttranslate("Mana Convergence"),
      'typeline' => clienttranslate("Mana Token"),
      'type'  => SPELL,
      'flavorText'  => clienttranslate('Mana is the cement that binds ideas and reality.'),
      'artist' => "HuoMiao Studio",
      'subtypes'  => [],
      'effectDesc' => clienttranslate('This card cannot be played. It can be put in Mana.'),
      'costHand' => 0,
      'costReserve' => 0,
    ];
  }

  public function canBePlayed($player, $scout = false, $reserveFlipCost = false)
  {
    return false;
  }

  // Empty = no legal zone: drops it from free plays (ChooseAssignment) and makes PlayCard::isDoable false.
  public function getPlayableLocation($player, $forcedLocation = null, $free = false)
  {
    return [];
  }
}
