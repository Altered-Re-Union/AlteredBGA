<?php
namespace ALT\Cards\AX;
use ALT\Helpers\FT;

class AX_Exalted_PlagueofAvarice extends \ALT\Models\Card
{
  public function __construct($row){
		parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_AX_117_E',
      'asset'  => 'ALT_EOLE_B_AX_117_E',

      'faction'  => FACTION_AX,
      'rarity'  => RARITY_EXALTED,
      'name'  => clienttranslate("Plague of Avarice"),
      'typeline' => clienttranslate("Character - Corruption"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('The pursuit of progress is like an insatiable hunger.'),
      'artist' => "Khoa Viet",
      'extension'=>'ROC',
      'subtypes'  => [CORRUPTION],
      'effectDesc' => clienttranslate('{H} Each Permanent you control activates its {j} abilities.  When you pass — Sacrifice a Permanent.'),
      'forest' => 2, 
      'mountain' => 3, 
      'ocean' => 3, 
      'costHand' => 5, 
      'costReserve' => 2, 
      'effectHand' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'activateAllPermanents']),
      'effectPassive' => [
        'EndTurn' => [
          'conditions' => ['isMe'],
          'output' => 
              FT::ACTION(TARGET, [
              'targetPlayer' => ME,
              'targetType' => [PERMANENT],
              'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice'])
            ])
        ]
      ]
    ];
  }
}
