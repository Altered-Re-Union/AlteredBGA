<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_HerostratusBurnerofTemples extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_137_R2',
      'asset' => 'ALT_FUGUE_B_BR_137_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Herostratus, Burner of Temples'),
      'typeline' => clienttranslate('Character - Soldier'),
      'type' => CHARACTER,
      'artist' => 'Gamon Studio',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER],
      'effectDesc' => clienttranslate('{H} You may discard #a card from your Reserve# to discard #target Permanent#.'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['mountain'],
      'effectHand' => FT::ACTION(
        TARGET,
        [
          'targetPlayer' => ME,
          'targetLocation' => [RESERVE],
          'targetType' => [CHARACTER, TOKEN, SPELL, PERMANENT],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []),
            FT::ACTION(TARGET, [
              'targetType' => [PERMANENT],
              'effect' => FT::ACTION(DISCARD, []),
            ])
          ),
        ],
        ['optional' => true]
      ),
    ];
  }
}
