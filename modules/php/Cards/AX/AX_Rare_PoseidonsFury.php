<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_PoseidonsFury extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_143_R1',
      'asset' => 'ALT_FUGUE_B_AX_143_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Poseidon\'s Fury'),
      'typeline' => clienttranslate('Spell'),
      'type' => SPELL,
      'artist' => 'Kevin Sidharta',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('$<FLEETING>.  #Choose one#:  • Send to Reserve #any number of target Characters# with #total# {O} 4 or less.  • #Discard target Permanent.#'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::SEQ(FT::GAIN(ME, FLEETING),
        FT::XOR(
          FT::ACTION(TARGET, ['upTo' => true, 'n' => INFTY, 'totalOcean' => 4, 'effect' => FT::DISCARD_TO_RESERVE()]),
          FT::ACTION(TARGET, ['targetType' => [PERMANENT], 'effect' => FT::ACTION(DISCARD, [])])
        )),
    ];
  }
}
