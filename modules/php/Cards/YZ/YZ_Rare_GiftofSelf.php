<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_GiftofSelf extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_20_R1',
      'asset' => 'ALT_CORE_B_YZ_20_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Gift of Self'),
      'typeline' => clienttranslate('Spell - Conjuration'),
      'type' => SPELL,
      'flavorText' => clienttranslate(
        'Her being begins to disintegrate as she breaks all the Mana bridges linking the idea of who she is to physical matter.'
      ),
      'artist' => 'Fahmi Fauzi',
      'subtypes' => [CONJURATION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Sacrifice a Character. If you do, draw #three cards#.'),
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['costHand', 'costReserve'],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(CHECK_CONDITION, [
          'condition' => 'canSacrifice',
          'effect' => FT::SEQ(
            FT::ACTION(TARGET, [
              'targetPlayer' => ME,
              'targetType' => [CHARACTER, TOKEN],
              'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            ]),
            FT::ACTION(DRAW, ['players' => ME, 'n' => 3])
          ),
        ])
      ),
    ];
  }
}
