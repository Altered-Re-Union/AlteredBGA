<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Exalted_HestiaGoddessoftheHearth extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_140_E',
      'asset' => 'ALT_FUGUE_B_BR_140_E',
      'faction' => FACTION_BR,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Hestia, Goddess of the Hearth'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'extension' => 'NEJ',
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('Temple {2}. (You may play me for {2} as a Landmark Permanent - Construction with: "At Noon — You may send me to Reserve.")  At Noon — Each Character with no boost in your Reserve gains 1 boost.'),
      'forest' => 3,
      'mountain' => 4,
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
                'effect' => 'boostReserve',
                'args' => ['noBoostIfBoosted' => true],
              ]),
            ],
          ],
        ],
      ],
    ];
  }
}
