<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Exalted_Charybdis extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_144_E',
      'asset' => 'ALT_FUGUE_B_BR_144_E',
      'faction' => FACTION_BR,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Charybdis'),
      'typeline' => clienttranslate('Character - Leviathan'),
      'type' => CHARACTER,
      'artist' => 'Jean-Baptiste Andrier',
      'extension' => 'NEJ',
      'subtypes' => [LEVIATHAN],
      'effectDesc' => clienttranslate('Gigantic.  If I\'m in {O}, I am <TOUGH_CHA_P_1>.  {R} $<SABOTAGE>. Then, you may discard target Permanent.'),
      'forest' => 3,
      'mountain' => 4,
      'ocean' => 4,
      'costHand' => 7,
      'costReserve' => 6,
      'gigantic' => true,
      'dynamicTough' => 'tough1:isInBiome:ocean:true',
      'effectHand' => FT::SEQ(
        FT::SABOTAGE(), 
        FT::ACTION(TARGET, [
          'targetType' => [PERMANENT],
          'effect' => FT::ACTION(DISCARD, []),
        ], ['optional' => true]),
      ),
    ];
  }
}
