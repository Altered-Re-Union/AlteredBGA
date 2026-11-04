<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Common_Melantho extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_134_C',
      'asset' => 'ALT_FUGUE_B_AX_134_C',
      'faction' => FACTION_AX,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Melantho'),
      'typeline' => clienttranslate('Character - Rogue'),
      'flavorText'  => clienttranslate('"Do and undo...This must end."'),
      'artist' => 'Zaeliven',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [ROGUE],
      'effectDesc' => clienttranslate('{R} $<SABOTAGE>. (Discard up to one target card from a Reserve.)'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 0,
      'costHand' => 2,
      'costReserve' => 2,
      'effectReserve' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
    ];
  }
}
