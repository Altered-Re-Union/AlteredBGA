<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_OrdisCounterspy extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_135_R2',
      'asset' => 'ALT_FUGUE_B_OR_135_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Ordis Counterspy'),
      'typeline' => clienttranslate('Character - Bureaucrat Soldier'),
      'type' => CHARACTER,
      'artist' => 'DOBA',
      'extension' => 'NEJ',
      'subtypes' => [BUREAUCRAT, SOLDIER],
      'effectDesc' => clienttranslate('{H} #Choose one card in each player\'s Reserve. Discard all other cards in Reserve.#  {R} Create an Ordis Recruit 1/1/1 Soldier token in my Expedition.'),
      'forest' => 2,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 3,
      'costReserve' => 3,
      'effectHand' => FT::ACTION(SPECIAL_EFFECT, ['effect' => 'eachPlayerKeepOneReserve']),
      'effectReserve' => FT::ACTION(INVOKE_TOKEN, [
        'pId' => 'source',
        'tokenType' => 'OD_Common_OrdisRecruit',
        'targetLocation' => ['source'],
      ]),
    ];
  }
}
