<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Exalted_DionysosGodofFeasts extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_141_E',
      'asset' => 'ALT_FUGUE_B_LY_141_E',
      'faction' => FACTION_LY,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Dionysos, God of Feasts'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'artist' => 'Nestor Papatriantafyllou',
      'extension' => 'NEJ',
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('Temple {2}. (You may play me for {2} as a Landmark Permanent - Construction with: "At Noon — You may send me to Reserve.")  The first time you roll a die each Day — Target Character gains 1 boost.'),
      'forest' => 3,
      'mountain' => 3,
      'ocean' => 4,
      'costHand' => 3,
      'costReserve' => 3,
      'costTemple' => 2,
      'effectPassive' => [
        'Noon' => [
          'conditions' => ['isMe', 'isTemple'],
          'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => RESERVE], ['optional' => true]),
        ],
        'RollDie' => [
          'conditions' => ['isMe', 'hasNotRolledDieThisDay'],
          'output' => FT::ACTION(TARGET, [
            'targetType' => [CHARACTER],
            'effect' => FT::GAIN(EFFECT, BOOST),
          ]),
        ],
      ],
    ];
  }
}
