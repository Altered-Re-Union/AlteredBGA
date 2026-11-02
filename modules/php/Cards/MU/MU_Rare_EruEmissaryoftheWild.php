<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_EruEmissaryoftheWild extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CYCLONE_B_MU_68_R1',
      'asset'  => 'ALT_CYCLONE_B_MU_68_R',

      'faction'  => FACTION_MU,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Eru, Emissary of the Wild"),
      'typeline' => clienttranslate("Character - Druid"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('"Surrounded by all of his animal friends, my teacher never felt alone, even living as a hermit." - Kauri'),
      'artist' => "Nestor Papatriantafyllou",
      'extension' => 'SO',
      'subtypes'  => [DRUID],
      'effectDesc' => clienttranslate('#{H} You may have target Character facing me gain <ANCHORED>. If you do, I gain $<ANCHORED>.#'),
      'forest' => 4,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['mountain', 'ocean'],
      'effectHand' => FT::ACTION(TARGET, [
        'targetLocation' => ['opponentSource'],
        'upTo' => true,
        'effect' => FT::ACTION(CHECK_CONDITION, [
          'condition' => 'isTargetNotAnchored',
          'effect' => FT::SEQ(
            FT::GAIN(EFFECT, ANCHORED),
            FT::GAIN(ME, ANCHORED)
          )
        ])
      ])
    ];
  }
}
