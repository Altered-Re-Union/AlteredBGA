<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_RinkuAltrunAce extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_139_R2',
      'asset' => 'ALT_FUGUE_B_BR_139_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Rinku, Altrun Ace'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('Jonathan Sukenik, World Champion.'),
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{J} Target a Character you control. I gain as many boosts as its highest statistic, up to a max of #4 boosts# on me.'),
      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER],
        'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostTargetHighestStat', 'args' => ['maxBoosts' => 4]]),
      ]),
    ];
  }
}
