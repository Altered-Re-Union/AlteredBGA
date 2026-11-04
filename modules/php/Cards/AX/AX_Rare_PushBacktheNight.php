<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Rare_PushBacktheNight extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_YZ_121_R2',
      'asset'  => 'ALT_EOLE_B_YZ_121_R',

      'faction'  => FACTION_AX,
      'rarity'  => RARITY_RARE,
      'name'  => clienttranslate("Push Back the Night"),
      'typeline' => clienttranslate("Landmark_permanent - Feat"),
      'type'  => PERMANENT,
      'flavorText'  => clienttranslate('"Fear shall not pass!"'),
      'artist' => "Justice Wong",
      'extension' => 'ROC',
      'subtypes'  => [FEAT, LANDMARK],
      'effectDesc' => clienttranslate('{J} Draw a card, #then put a card from your hand in Reserve.#  At Noon — If six or more cards are in your discard pile, complete me.'),
      'supportDesc' => clienttranslate('<COMPLETED_LOW>: When #a card goes directly from your hand to Reserve# — You may exhaust me ({T}) to create a #<BRASSBUG> Robot# token in target Expedition.'),
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['costHand', 'costReserve'],
       'effectPlayed' => FT::SEQ(
        FT::ACTION(DRAW, ['players' => ME]),
        FT::ACTION(
          TARGET,
          [
            'targetType' => [CHARACTER, SPELL, PERMANENT],
            'targetPlayer' => ME,
            'targetLocation' => [HAND],
            'effect' => FT::DISCARD_TO_RESERVE(),
          ],
        ),
      ),
      'effectPassive' => [
        'Noon' => [
          'conditions' => ['isMe', 'isThisFeatIncomplete', 'hasDiscardPileCards:6'],
          'output' => FT::ACTION(COMPLETE_FEAT, ['cardId' => 'source']),
        ],
        'Discard' => [
          'conditions' => ['hasSameOwner', 'isThisFeatCompleted', 'isDiscarded:hand:reserve', 'notTapped'],
          'output' => FT::SEQ_OPTIONAL(
            FT::ACTION(TAP, []),
            FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => 'AX_Common_Brassbug',
              'targetLocation' => STORMS,
            ])
          ),
        ],
      ],
    ];
  }
}
