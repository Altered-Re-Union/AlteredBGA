<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_TriremeQuartermaster extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_OR_134_R2',
      'asset' => 'ALT_FUGUE_B_OR_134_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Trireme Quartermaster'),
      'typeline' => clienttranslate('Character - Soldier, Trainer'),
      'type' => CHARACTER,
      'flavorText' => clienttranslate('Look sharp, scallywags! I see our next adventure on the horizon!'),
      'artist' => 'Saeed Jalabi',
      'extension' => 'NEJ',
      'subtypes' => [SOLDIER, TRAINER],
      'effectDesc' => clienttranslate('#Your Companions have : "{R} Create an <ORDIS_RECRUIT> Soldier token in my Expedition."#'),
      'forest' => 0,
      'mountain' => 2,
      'ocean' => 2,
      'costHand' => 2,
      'costReserve' => 2,
      'changedStats' => ['mountain', 'ocean'],
      'effectPassive' => [
        'ChooseAssignment' => [
          'conditions' => ['isCardPlayed:companion', 'hasSameOwner'],
          'output' => FT::ACTION(INVOKE_TOKEN, [
            'pId' => 'source',
            'tokenType' => 'OD_Common_OrdisRecruit',
            'targetLocation' => ['eventTo'],
          ]),
        ],
      ],
    ];
  }
}
