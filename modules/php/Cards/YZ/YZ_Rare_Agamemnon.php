<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_Agamemnon extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_139_R1',
      'asset' => 'ALT_FUGUE_B_YZ_139_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Agamemnon'),
      'typeline' => clienttranslate('Character - Noble, Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('If his hand does not tremble, Iphigenia\'s does not resist.'),
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE, SOLDIER],
      'effectDesc' => clienttranslate('#{J}# Sacrifice a Character.  #When you sacrifice another Character — I gain 2 boosts.#'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['forest', 'mountain', 'ocean'],
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER, TOKEN],
        'targetLocation' => [STORM_LEFT, STORM_RIGHT],
        'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
      ]),
      'effectPassive' => [
        'Discard' => [
          'conditions' => ['isMe', 'isSacrifice:character', 'excludeSelf'],
          'output' => FT::GAIN(ME, BOOST, 2),
        ],
      ],
    ];
  }
}
