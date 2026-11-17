<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Exalted_ClosetheBreach extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_YZ_122_E',
      'asset'  => 'ALT_EOLE_B_YZ_122_E',

      'faction'  => FACTION_YZ,
      'rarity'  => RARITY_EXALTED,
      'name'  => clienttranslate("Close the Breach!"),
      'typeline' => clienttranslate("Landmark_permanent - Feat"),
      'type'  => PERMANENT,
      'flavorText'  => clienttranslate('"Close the breach? I was just getting warmed up…"'),
      'artist' => "Anh Tung & Ba Vo",
      'extension' => 'ROC',
      'subtypes'  => [FEAT, LANDMARK],
      'effectDesc' => clienttranslate('{J} Discard target Character with Base Cost {3} or less.  When you pass after your opponents — If you are first player, complete me.'),
      'supportDesc' => clienttranslate('<COMPLETED_LOW>: {T}, {1} : Target Character you control gains 1 boost, then <AFTER_YOU>.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER, TOKEN],
        'maxBaseCost' => 3,
        'upTo' => true,
        'effect' => FT::ACTION(DISCARD, []),
      ]),
      'effectTap' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'isThisFeatCompleted',
        'effect' => FT::SEQ(
          FT::ACTION(PAY, ['pay' => 1]),
          FT::ACTION(TARGET, [
            'targetType' => [CHARACTER],
            'targetPlayer' => ME,
            'effect' => FT::GAIN(EFFECT, BOOST),
          ]),
          // Keep AFTER_YOU outside TARGET: it still resolves when there is no character to boost.
          FT::ACTION(AFTER_YOU, []),
        ),
      ]),
      'effectPassive' => [
        // Pass uses EndTurn listener in this rules engine.
        'EndTurn' => [
          'conditions' => ['isMe', 'isThisFeatIncomplete'],
          'output' => FT::ACTION(CHECK_CONDITION, [
            // "After your opponents" means you are not the first to pass this day.
            'condition' => 'isFirstPassing',
            'oppositeEffect' => FT::ACTION(CHECK_CONDITION, [
              'condition' => 'isFirstPlayer',
              'effect' => FT::ACTION(COMPLETE_FEAT, ['cardId' => 'source']),
            ]),
          ]),
        ],
      ],
    ];
  }
}
