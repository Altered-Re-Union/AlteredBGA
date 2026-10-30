<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_PenelopesSuitors extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_135_R1',
      'asset' => 'ALT_FUGUE_B_YZ_135_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Penelope\'s Suitors'),
      'typeline' => clienttranslate('Character - Noble, Rogue'),
      'artist' => 'Zaeliven',
      'extension' => 'NEJ',
      'type' => CHARACTER,
      'subtypes' => [NOBLE, ROGUE],
      'effectDesc' => clienttranslate('{H} #You may discard a card from your Reserve# to Sabotage a card with Reserve Cost {2} or less.'),
      'forest' => 0,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 1,
      'costReserve' => 1,
      'changedStats' => ['costHand'],
      'effectHand' => FT::SEQ_OPTIONAL(
        FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'effect' => FT::ACTION(DISCARD, []),
        ]),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER, SPELL, TOKEN, PERMANENT],
          'targetLocation' => [RESERVE],
          'effect' => FT::ACTION(DISCARD, []),
          'maxReserveCost' => 2,
        ]),
      ),
    ];
  }
}
