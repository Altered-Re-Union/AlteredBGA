<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_Boom extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_21_R1',
      'asset' => 'ALT_CORE_B_AX_21_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Boom!'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'flavorText' => clienttranslate('"Catastrophic failure? I prefer the term \'learning opportunity\'."'),
      'artist' => 'HuoMiao Studio',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate(
        '$<FLEETING>.  Sacrifice a Character #or Permanent#. If you do, discard target Character or Permanent.'
      ),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN, PERMANENT],
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            FT::ACTION(TARGET, [
              'targetType' => [CHARACTER, TOKEN, PERMANENT],
              'effect' => FT::ACTION(DISCARD, []),
            ])
          ),
        ])
      ),
    ];
  }
}
