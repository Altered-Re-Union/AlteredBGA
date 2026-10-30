<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_EchoChamber extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_143_C',
      'asset' => 'ALT_FUGUE_B_OR_143_C',
      'faction' => FACTION_OD,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Echo Chamber'),
      'typeline' => clienttranslate('Spell - Conjuration'),
      'type' => SPELL,
      'flavorText' => clienttranslate('"Through the bond of Gestalt, I can still communicate with my brother." - Eyota'),
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [CONJURATION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Create your Hero\'s Signature token in your Reserve.'),
      'costHand' => 1,
      'costReserve' => 1,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(CHECK_CONDITION, [
          'condition' => 'hasHeroSignatureToken',
          'effect' => FT::ACTION(INVOKE_TOKEN, [
              'pId' => 'source',
              'tokenType' => HERO_SIGNATURE,
              'targetLocation' => [RESERVE],
            ]),
        ]),
      ),
    ];
  }
}
