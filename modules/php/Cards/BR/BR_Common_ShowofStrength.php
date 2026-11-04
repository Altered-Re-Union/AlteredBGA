<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Common_ShowofStrength extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_146_C',
      'asset' => 'ALT_FUGUE_B_BR_146_C',
      'faction' => FACTION_BR,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Show of Strength'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Ahn Tung',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Target Character in your Reserve gains 2 boosts. Then, $<SABOTAGE>.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'targetLocation' => [RESERVE],
          'targetPlayer' => ME,
          'effect' => FT::GAIN(EFFECT, BOOST, 2),
        ]),
        FT::SABOTAGE(),
      ),
    ];
  }
}
