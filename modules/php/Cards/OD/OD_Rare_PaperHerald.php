<?php

namespace ALT\Cards\OD;

use ALT\Helpers\FT;

class OD_Rare_PaperHerald extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_04_R1',
      'asset' => 'ALT_CORE_B_OR_04_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Paper Herald'),
      'typeline' => clienttranslate('Character - Messenger'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Words mean more than what is set down on paper.'),
      'artist' => 'Jean-Baptiste Andrier',
      'subtypes' => [MESSENGER],
      'supportDesc' => clienttranslate(
        '{D} : Create an <ORDIS_RECRUIT> Soldier token in target Expedition. (Discard me from Reserve to do this.)'
      ),
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 2,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['mountain', 'ocean'],
      'supportIcon' => 'discard',
      'effectSupport' => FT::ACTION(INVOKE_TOKEN, [
        'pId' => 'source',
        'tokenType' => 'OD_Common_OrdisRecruit',
      ]),
    ];
  }
}
