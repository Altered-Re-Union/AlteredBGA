<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Exalted_HephaestusGodoftheForge extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_138_E',
      'asset' => 'ALT_FUGUE_B_AX_138_E',
      'faction' => FACTION_AX,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Hephaestus, God of the Forge'),
      'typeline' => clienttranslate('Character - Deity'),
      'type' => CHARACTER,
      'subtypes' => [DEITY],
      'effectDesc' => clienttranslate('<TEMPLE> {2}. (You may play me for {2} as a Landmark Permanent - Construction with: "At Noon — You may send me to Reserve.") When you pass — You may pay {1} to draw a card.'),
      'forest' => 3,
      'mountain' => 4,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'costTemple' => 2,
      'effectPassive' => [
        'EndTurn' => [
          'conditions' => ['isMe'],
          'output' => FT::SEQ_OPTIONAL_MANUAL(
            FT::ACTION(PAY, ['pay' => 1]),
            FT::ACTION(DRAW, ['players' => ME])
          ),
        ],
        'Noon' => [
          'conditions' => ['isMe', 'isTemple'],
          'output' => FT::ACTION(DISCARD, ['cardId' => ME, 'destination' => RESERVE], ['optional' => true]),
        ],
      ],
    ];
  }
}