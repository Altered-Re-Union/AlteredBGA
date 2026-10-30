<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_OfferingtotheGods extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_YZ_132_C',
      'asset' => 'ALT_FUGUE_B_YZ_132_C',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Offering to the Gods'),
      'typeline' => clienttranslate('Spell - Disruption'),
      'type' => SPELL,
      'artist' => 'Nestor Papatriantafyllou',
      'extension' => 'NEJ',
      'subtypes' => [DISRUPTION],
      'effectDesc' => clienttranslate('$<FLEETING>.  Sacrifice a Character. If you do, target opponent sacrifices a Character that it was facing.'),
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::SEQ(
        FT::GAIN(ME, FLEETING),
        FT::SEQ_OPTIONAL(
          FT::ACTION(TARGET, [
            'targetPlayer' => ME,
            'targetType' => [CHARACTER, TOKEN],
            'effect' => FT::SEQ(
              FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
              FT::ACTION(TARGET_PLAYER, [
                'opponentsOnly' => true,
                'effect' => FT::ACTION(TARGET, [
                  'targetPlayer' => ME,
                  'targetType' => [CHARACTER, TOKEN],
                  'targetLocation' => ['source'],
                  'effect' => FT::ACTION(DISCARD, ['desc' => 'sacrifice']),
                ]),
              ]),
            ),
          ]),
        )
      ),
    ];
  }
}
