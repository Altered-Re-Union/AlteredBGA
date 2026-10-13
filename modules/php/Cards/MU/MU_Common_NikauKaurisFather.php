<?php

namespace ALT\Cards\MU;

use ALT\Helpers\FT;

class MU_Common_NikauKaurisFather extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_MU_112_C',
      'asset'  => 'ALT_EOLE_B_MU_112_C',
      'faction'  => FACTION_MU,
      'rarity'  => RARITY_COMMON,
      'name'  => clienttranslate("Nikau, Kauri's Father"),
      'typeline' => clienttranslate("Character - Citizen"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('"Kauri, remember that courage is the happy medium between fear and recklessness!" — Nikau'),
      'artist' => "Kevin Sidharta",
      'extension' => 'ROC',
      'subtypes'  => [CITIZEN],
      'effectDesc' => clienttranslate('When an Animal joins your Expeditions — I gain 1 boost, up to a max of 2.'),
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['isCardAddedAnyPlayer:character:::true', 'isCardAddedAnyPlayer:animal:::true'],
          'output' => FT::GAIN(ME, BOOST, 1, 2),
        ],
        'InvokeToken' => [
          'conditions' => ['isCardAddedAnyPlayer:character:::true', 'isCardAddedAnyPlayer:animal:::true'],
          'output' => FT::GAIN(ME, BOOST, 1, 2),
        ],
        'MoveCard' => [
          'conditions' => ['isCardAddedAnyPlayer:character:::true', 'isCardAddedAnyPlayer:animal:::true'],
          'output' => FT::GAIN(ME, BOOST, 1, 2),
        ],
      ],
    ];
  }
}
