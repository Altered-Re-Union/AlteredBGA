<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_MastTiedOdysseus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_135_R2',
      'asset' => 'ALT_FUGUE_B_MU_135_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Mast-Tied Odysseus'),
      'typeline' => clienttranslate('Character - Adventurer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('There are multiple things one can be tied to in order to experience something unique.'),
      'artist' => 'Benoit Barraqué-Curie',
      'extension' => 'NEJ',
      'subtypes' => [ADVENTURER],
      'effectDesc' => clienttranslate('#{R}# You may discard a card from your Reserve. If you do, I gain $<ANCHORED>.'),
      'forest' => 3,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 2,
      'effectReserve' => FT::ACTION(
        TARGET,
        [
          'targetType' => [CHARACTER, SPELL, PERMANENT],
          'targetPlayer' => ME,
          'targetLocation' => [RESERVE],
          'upTo' => true,
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, []), 
            FT::GAIN(ME, ANCHORED)
          ),
        ],
        ['optional' => true]
      ),
    ];
  }
}
