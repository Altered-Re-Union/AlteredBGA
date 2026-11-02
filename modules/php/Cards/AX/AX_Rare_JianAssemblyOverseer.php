<?php

namespace ALT\Cards\AX;

use ALT\Helpers\FT;

class AX_Rare_JianAssemblyOverseer extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_AX_10_R1',
      'asset' => 'ALT_CORE_B_AX_10_R',

      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Jian, Assembly Overseer'),
      'type' => CHARACTER,
      'subtypes' => [ENGINEER],
      'supportDesc' => clienttranslate(
        '#{D} : Activate the {j} abilities of target Permanent you control.# (Discard me from Reserve to do this.)'
      ),
      'supportIcon' => 'discard',
      'flavorText' => clienttranslate(
        '"This material does not seem to be affected by gravity. By harnessing the properties of this Aerolith, we could create flying ships and cities, and fly close to the clouds..."'
      ),
      'typeline' => clienttranslate('Character - Engineer'),
      'artist' => 'Khoa Viet',

      'forest' => 3,
      'mountain' => 2,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 2,
      'effectSupport' => FT::ACTION(TARGET, [
        'targetType' => [PERMANENT],
        'targetPlayer' => ME,
        'hasEffects' => ['Played'],
        'effect' => FT::ACTION(ACTIVATE_EFFECT, []),
      ]),
    ];
  }
}
