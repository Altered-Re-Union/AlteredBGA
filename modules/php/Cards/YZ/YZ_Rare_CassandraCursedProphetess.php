<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_CassandraCursedProphetess extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_133_R1',
      'asset' => 'ALT_FUGUE_B_YZ_133_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Cassandra, Cursed Prophetess'),
      'typeline' => clienttranslate('Character - Mage'),
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [MAGE],
      'effectDesc' => clienttranslate('{H} You may reveal a Spell with Hand Cost {4} or more from your hand. If you don\'t, I gain Fleeting. #If the Hand Cost is {7} or more, draw a card.#'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 3,
      'costHand' => 2,
      'costReserve' => 2,
      'effectHand' => FT::XOR(
        FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetLocation' => [HAND],
          'targetType' => [SPELL],
          'minHandCost' => 4,
          'effect' => FT::SEQ(
            FT::ACTION(SPECIAL_EFFECT, ['effect' => 'reveal']),
            FT::ACTION(CHECK_CONDITION, [
              'conditions' => ['cardPlayedCostCheck:7:hand'],
              'effect' => FT::ACTION(DRAW, ['players' => ME], ['pId' => 'owner']),
            ]),
          ),
        ]),
        FT::GAIN(ME, FLEETING)
      ),
    ];
  }
}
