<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_ChargingRam extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_BR_131_R2',
      'asset' => 'ALT_FUGUE_B_BR_131_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Charging Ram'),
      'typeline' => clienttranslate('Character - Animal'),
      'type' => CHARACTER,
      'artist' => 'Julien Carrasco',
      'extension' => 'NEJ',
      'flavorText'  => clienttranslate('Polyphemus\' gluttony nearly wiped out the species...but to him, that\'s the sheep\'s problem.'),
      'subtypes' => [ANIMAL],
      'effectDesc' => clienttranslate('#{H} You may give me $<FLEETING> to give 1 boost to another target Character.#'),
      'forest' => 0,
      'mountain' => 2,
      'ocean' => 0,
      'costHand' => 1,
      'costReserve' => 1,
      'effectHand' => FT::SEQ_OPTIONAL(FT::GAIN(ME, FLEETING), FT::ACTION(TARGET, ['excludeSelf' => true, 'effect' => FT::ACTION(GAIN, ['type' => BOOST])])),
    ];
  }
}
