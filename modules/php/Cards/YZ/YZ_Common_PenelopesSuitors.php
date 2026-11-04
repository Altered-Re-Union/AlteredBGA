<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_PenelopesSuitors extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_135_C',
      'asset' => 'ALT_FUGUE_B_YZ_135_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Penelope\'s Suitors'),
      'typeline' => clienttranslate('Character - Noble, Rogue'),
      'type' => CHARACTER,
      'artist' => 'Zaeliven',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE, ROGUE],
      'effectDesc' => clienttranslate('{H} $<SABOTAGE> a card with Reserve Cost {2} or less.'),
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 1,
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
        'maxReserveCost' => 2,
      ]),
    ];
  }
}
