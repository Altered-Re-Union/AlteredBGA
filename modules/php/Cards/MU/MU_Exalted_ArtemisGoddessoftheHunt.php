<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Exalted_ArtemisGoddessoftheHunt extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_138_E',
      'asset' => 'ALT_FUGUE_B_MU_138_E',
      'faction' => FACTION_MU,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Artemis, Goddess of the Hunt'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'artist' => 'Gamon Studio',
      'extension' => 'NEJ',
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('Temple {2}. (You may play me for {2} as a Landmark Permanent - Construction with: "At Noon — You may send me to Reserve.")  At Noon — Each Character in your Expeditions gain 1 boost.'),
      'forest' => 4,
      'mountain' => 3,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'costTemple' => 2,
      'effectPassive' => [
        'Noon' => [
          'childs' => [
            [
              'conditions' => ['isMe', 'isTemple'],
              'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => RESERVE], ['optional' => true]),
            ],
            [
              'conditions' => ['isMe'],
              'output' => FT::ACTION(SPECIAL_EFFECT, [
                'effect' => 'boostExpeditions',
                'args' => [],
              ]),
            ],
          ],
        ],
      ],
    ];
  }
}
