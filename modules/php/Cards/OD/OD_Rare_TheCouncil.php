<?php

namespace ALT\Cards\OD;

class OD_Rare_TheCouncil extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_CORE_B_OR_12_R1',
      'asset' => 'ALT_CORE_B_OR_12_R',

      'faction' => FACTION_OD,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('The Council'),
      'typeline' => clienttranslate('Character - Bureaucrat'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate(
        'The idea of facing the Council is deterrent enough for most would-be criminals in Asgartha.'
      ),
      'artist' => 'Jean-Baptiste Andrier',
      'subtypes' => [BUREAUCRAT],
      'effectDesc' => clienttranslate('The {j}, {h} and {r} abilities of Characters facing me can\'t activate.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['forest', 'mountain', 'ocean', 'costHand', 'costReserve'],
      'blockingPower' => true,
    ];
  }
}
