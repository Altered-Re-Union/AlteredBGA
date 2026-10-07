<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_CassandraCursedProphetess extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_133_C',
      'asset' => 'ALT_FUGUE_B_YZ_133_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Cassandra, Cursed Prophetess'),
      'typeline' => clienttranslate('Character - Mage'),
      'type' => CHARACTER,
      'subtypes' => [MAGE],
      'flavorText' => clienttranslate('Doom foretold, never heeded.'),
      'artist' => 'Eilene Cherie',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('{H} You may reveal a Spell with Hand Cost {4} or more from your hand. If you don\'t, I gain Fleeting.'),
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
          'effect' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'reveal']),
        ]),
        FT::GAIN(ME, FLEETING)
      ),
    ];
  }
}
