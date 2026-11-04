<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_Eurylochus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_136_R2',
      'asset' => 'ALT_FUGUE_B_BR_136_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Eurylochus'),
      'typeline' => clienttranslate('Character - Soldier, Rogue'),
      'type' => CHARACTER,
      'flavorText'  => clienttranslate('"I though the point of having Odysseus as guide was to avoid all these perils..."'),
      'artist' => 'Alexandre Bonvalot',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, ROGUE],
      'effectDesc' => clienttranslate('{R} $<SABOTAGE>. #If you discarded a card with Reserve Cost {1} or less this way, Resupply.#'),
      'forest' => 1,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 3,
      'changedStats' => ['forest'],
      'effectReserve' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, TOKEN, SPELL, PERMANENT],
        'targetLocation' => [RESERVE],
        'upTo' => true,
        'effect' => FT::SEQ(
          FT::ACTION(DISCARD, []),
          FT::ACTION(CHECK_CONDITION, [
            'condition' => 'costCheck:1:LTE:reserve',
            'effect' => FT::ACTION(RESUPPLY, []),
          ]),
        ),
      ]),
    ];
  }
}
