<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Exalted_FaneofHelios extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_AX_141_E',
      'asset' => 'ALT_FUGUE_B_AX_141_E',
      'faction' => FACTION_AX,
      'rarity' => RARITY_EXALTED,
      'name' => clienttranslate('Fane of Helios'),
      'typeline' => clienttranslate('Landmark Permanent - Construction'),
      'type' => PERMANENT,
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [CONSTRUCTION, LANDMARK],
      'effectDesc' => clienttranslate('<TOUGH_1>.  At Noon — Draw two cards, then you may put a card from your hand in your Mana zone, as a ready Mana Orb.'),
      'costHand' => 6,
      'costReserve' => 6,
      'frame' => 2,
      'tough' => 1,
      'effectPassive' => [
        'Noon' => [
          'conditions' => ['isMe'],
          'output' => FT::SEQ(
            FT::ACTION(DRAW, ['n' => 2, 'players' => ME]),
            FT::ACTION(TARGET, [
              'targetLocation' => [HAND],
              'targetType' => [CHARACTER, PERMANENT, SPELL],
              'targetPlayer' => ME,
              'upTo' => true,
              'effect' => FT::ACTION(DISCARD, [
                'destination' => MANA,
                'tapped' => false,
                'force' => true,
              ]),
            ])
          )
        ],
      ]
    ];
  }
}