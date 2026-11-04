<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_AxiomCarpenter extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_140_R1',
      'asset' => 'ALT_FUGUE_B_AX_140_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Axiom Carpenter'),
      'typeline' => clienttranslate('Character - Engineer'),
      'artist' => 'Jefrey Yonathan',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [ENGINEER],
      'effectDesc' => clienttranslate('#{J}# If you control two or more Constructions, I gain 1 boost.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasControl:construction:2',
        'effect' => FT::GAIN(ME, BOOST),
      ]),
    ];
  }
}
