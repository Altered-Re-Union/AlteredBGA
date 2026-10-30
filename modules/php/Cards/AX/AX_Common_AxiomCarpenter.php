<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_AxiomCarpenter extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_140_C',
      'asset' => 'ALT_FUGUE_B_AX_140_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Axiom Carpenter'),
      'typeline' => clienttranslate('Character - Engineer'),
      'artist' => 'Jefrey Yonathan',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [ENGINEER],
      'effectDesc' => clienttranslate('{R} If you control two or more Constructions, I gain 1 boost.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'effectReserve' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasControl:construction:2',
        'effect' => FT::GAIN(ME, BOOST),
      ]),
    ];
  }
}
