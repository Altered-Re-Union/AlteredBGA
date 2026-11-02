<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_LadyoftheLake extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_YZ_09_R1',
      'asset' => 'ALT_CORE_B_YZ_09_R',

      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Lady of the Lake'),
      'typeline' => clienttranslate('Character - Fairy Spirit'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"One day, I will show you the wonders of Avalon and the true might of the Fae realms."'),
      'artist' => 'Nestor Papatriantafyllou',
      'subtypes' => [FAIRY, SPIRIT],
      'supportDesc' => clienttranslate(
        '#{D} : The next Spell you play this turn loses <FLEETING>.# (Discard me from Reserve to do this.)'
      ),
      'forest' => 1,
      'mountain' => 1,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'removeFleetingSpellPlayed']),
    ];
  }
}
