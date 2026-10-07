<?php
namespace ALT\Cards\MU;
use ALT\Helpers\FT;

class MU_Common_Eumaeus extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_MU_137_C',
      'asset' => 'ALT_FUGUE_B_MU_137_C',
      'faction' => FACTION_MU,
      'rarity' => RARITY_COMMON,
      'name' => clienttranslate('Eumaeus'),
      'typeline' => clienttranslate('Character - Druid'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('"We still have a long way to go."'),
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [DRUID],
      'effectDesc' => clienttranslate('{J} Target Character in your Reserve gains 1 boost.'),
      'forest' => 3,
      'mountain' => 1,
      'ocean' => 3,
      'costHand' => 3,
      'costReserve' => 3,
      'effectPlayed' => FT::ACTION(TARGET, [
        'targetPlayer' => ME,
        'targetType' => [CHARACTER],
        'targetLocation' => [RESERVE],
        'effect' => FT::GAIN(EFFECT, BOOST),
      ]),
    ];
  }
}
