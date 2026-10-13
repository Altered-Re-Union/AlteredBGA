<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_Rust extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_BR_123_C',
      'asset' => 'ALT_EOLE_B_BR_123_C',

      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Rust'),
      'type' => CHARACTER,
      'subtypes' => [COMPANION],
      'effectDesc' => clienttranslate('  {j} I gain 1 boost per Completed Feat in your Landmarks.'),
      'flavorText'  => clienttranslate('"I\'m not as rusty as that."'),
			'artist' => 'Tristan Bideau',

      'forest' => 0,
      'mountain' => 0,
      'ocean' => 0,
      'token' => true,
      'costHand' => 0,
      'costReserve' => 0,
      'typeline' => clienttranslate('Token Character - Companion'),
      // InvokeToken skips ChooseAssignment, so effectPlayed alone never runs for Rust.
      // The {j} is emulated as a passive listener, and must honour blocking power (eg. The Council).
      'effectPassive' => [
        'InvokeToken' => [
          'conditions' => ['isSelfPlayCardEvent', 'isCardAdded:character', 'isNotBlockedByOpponentPower'],
          'output' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'boostXCompletedFeat']),
        ],
      ],
    ];
  }
}
