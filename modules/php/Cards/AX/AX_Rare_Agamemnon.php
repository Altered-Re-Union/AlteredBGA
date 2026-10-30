<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_Agamemnon extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_139_R2',
      'asset' => 'ALT_FUGUE_B_YZ_139_R',
      'faction' => FACTION_AX,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Agamemnon'),
      'typeline' => clienttranslate('Character - Noble, Soldier'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('If his hand does not tremble, Iphigenia\'s does not resist.'),
      'artist' => 'Taras Susak',
      'extension' => 'NEJ',
      'subtypes' => [NOBLE, SOLDIER],
      'effectDesc' => clienttranslate('#{J}# Sacrifice a #Permanent#.  #When you sacrifice a Permanent — I gain 2 boosts.#'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'changedStats' => ['forest', 'mountain', 'ocean'],
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [PERMANENT],
        'targetLocation' => [STORM_LEFT, STORM_RIGHT, LANDMARK],
        'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
      ]),
      'effectPassive' => [
        'Discard' => [
          'conditions' => ['isMe', 'isSacrifice:permanent'],
          'output' => FT::GAIN(ME, BOOST, 2),
        ],
      ],
    ];
  }
}
