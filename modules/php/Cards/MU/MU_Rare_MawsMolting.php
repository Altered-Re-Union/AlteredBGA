<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Rare_MawsMolting extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_144_R2',
      'asset' => 'ALT_FUGUE_B_YZ_144_R',
      'faction' => FACTION_MU,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Maw\'s Molting'),
      'typeline' => clienttranslate('Spell - Conjuration'),
      'type' => SPELL,
      'flavorText' => clienttranslate('"I had to sacrifice my host to molt. I need another..."'),
      'artist' => 'Khoa Viet',
      'extension' => 'NEJ',
      'subtypes' => [CONJURATION],
      'effectDesc' => clienttranslate('$<FLEETING>.  #You may spend 2 boosts from Characters in your Expeditions to play me for {1} less#. Draw a card, then create your Hero\'s Signature token in your Reserve.'),
      'costHand' => 2,
      'costReserve' => 2,
      'costReductionSpendBoost' => ['reduction' => 1, 'boostCost' => 2],
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(DRAW, ['players' => ME, 'n' => 1]),
        FT::ACTION(CHECK_CONDITION, [
          'condition' => 'hasHeroSignatureToken',
          'effect' => FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => HERO_SIGNATURE,
              'targetLocation' => [RESERVE],
            ]),
        ]),
      ),
    ];
  }
}

