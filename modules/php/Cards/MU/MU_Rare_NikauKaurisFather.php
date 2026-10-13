<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Rare_NikauKaurisFather extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_MU_112_R1',
      'asset'  => 'ALT_EOLE_B_MU_112_R',

      'faction'  => FACTION_MU,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Nikau, Kauri's Father"),
      'typeline' => clienttranslate("Character - Citizen"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('"Kauri, remember that courage is the happy medium between fear and recklessness!" — Nikau'),
      'artist' => "Kevin Sidharta",
      'extension' => 'ROC',
      'subtypes'  => [CITIZEN],
      'effectDesc' => clienttranslate('When an Animal joins #your other Expedition# — #It gains# 1 boost.'),
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['isCardAddedAnyPlayer:character:::true', 'isCardAddedAnyPlayer:animal:::true', 'isPlayedInOwnOtherExpedition'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
        'InvokeToken' => [
          'conditions' => ['isCardAddedAnyPlayer:character:::true', 'isCardAddedAnyPlayer:animal:::true', 'isPlayedInOwnOtherExpedition'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
        'MoveCard' => [
          'conditions' => ['isCardAddedAnyPlayer:character:::true', 'isCardAddedAnyPlayer:animal:::true', 'isPlayedInOwnOtherExpedition'],
          'output' => FT::GAIN(EFFECT, BOOST),
        ],
      ],
    ];
  }
}
