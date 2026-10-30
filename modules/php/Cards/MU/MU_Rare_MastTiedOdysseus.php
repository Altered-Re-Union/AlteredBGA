<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_MastTiedOdysseus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_135_R1',
      'asset' => 'ALT_FUGUE_B_MU_135_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Mast-Tied Odysseus'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('{H} You may have #target opponent $<RESUPPLY>#. If you do, I gain $<ANCHORED>..'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectHand' => FT::SEQ_OPTIONAL_MANUAL(
        FT::ACTION(TARGET_PLAYER, [
          'opponentsOnly' => true,
          'effect' => FT::ACTION(RESUPPLY, []),
        ]),
        FT::GAIN(ME, ANCHORED)
      ),
    ];
  }
}
