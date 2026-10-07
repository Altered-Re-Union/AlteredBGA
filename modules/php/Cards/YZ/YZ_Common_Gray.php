<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_Gray extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_130_C',
      'asset' => 'ALT_FUGUE_B_YZ_130_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Gray'),
      'typeline' => clienttranslate('Yzmir Hero'),
      'type' => HERO,
      'artist' => 'Justice Wong',
      'extension' => 'NEJ',
      'effectDesc' => clienttranslate('{T}, Sacrifice a Character: Target Character gains 1 boost.  At Noon — If you\'re first player, create my Signature token: Molted Maw 0/0/0 in your Reserve (It\'s a Companion with Reserve Cost {1} and "When you sacrifice a Character — I gain 1 boost.")'),
      'reserveSlots' => 2,
      'landmarkSlots' => 2,
      'signatureToken' => 'YZ_Common_MoltedMaw',
      'effectTap' => FT::SEQ(
        FT::ACTION(TARGET, [
          'targetPlayer' => ME,
          'targetType' => [CHARACTER, TOKEN],
          'effect' => FT::SEQ(
            FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
            FT::ACTION(TARGET, ['effect' => FT::ACTION(GAIN, ['type' => BOOST])]),
          ),
        ]),
      ),
      'effectPassive' => [
        'Noon' => [
          'listeningConditions' => ['isMe'],
          'condition' => 'isFirstPlayer',
          'output' => [
            'action' => INVOKE_TOKEN,
            'automatic' => true,
            'args' => ['tokenType' => 'YZ_Common_MoltedMaw', 'targetLocation' => [RESERVE]],
          ],
        ],
      ],
    ];
  }
}
