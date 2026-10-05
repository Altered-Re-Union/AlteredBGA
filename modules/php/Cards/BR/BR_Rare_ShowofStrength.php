<?php
namespace ALT\Cards\BR;
use ALT\Helpers\FT;

class BR_Rare_ShowofStrength extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_146_R1',
      'asset' => 'ALT_FUGUE_B_BR_146_R',
      'faction' => FACTION_BR,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Show of Strength'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Ahn Tung',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Target Character in your Reserve gains #3# boosts. Then, $<SABOTAGE>.'),
      'costHand' => 2,
      'costReserve' => 2,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::ACTION(TARGET, [
          'targetType' => [CHARACTER],
          'targetLocation' => [RESERVE],
          'targetPlayer' => ME,
          'effect' => FT::GAIN(EFFECT, BOOST, 3),
        ]),
        FT::SABOTAGE(),
      ),
    ];
  }
}
