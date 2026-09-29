<?php
namespace ALT\Cards\OD;
use ALT\Helpers\FT;

class OD_Common_StigmaofBanality extends \ALT\Models\Card
{
  public function __construct($row){
		parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_OR_116_C',
      'asset'  => 'ALT_EOLE_B_OR_116_C',

      'faction'  => FACTION_OD,
      'rarity'  => RARITY_COMMON,
      'name'  => clienttranslate("Stigma of Banality"),
      'typeline' => clienttranslate("Character - Corruption"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('To be afraid of being different is to destroy individuality.'),
      'artist' => "Nestor Papatriantafyllou",
      'extension'=>'ROC',
      'subtypes'  => [CORRUPTION],
      'effectDesc' => clienttranslate('{H} You may discard target Character with Base Cost {2} or less. If you do, create an <ORDIS_RECRUIT> Soldier token in its Expedition. (Its Base Cost is the Reserve Cost if Fleeting, or the Hand Cost if not.)'),
      'forest' => 3, 
      'mountain' => 3, 
      'ocean' => 3, 
      'costHand' => 5, 
      'costReserve' => 3, 
      'effectHand' => FT::ACTION(TARGET, [
        'targetType' => [CHARACTER],
        'maxBaseCost' => 2,
        'effect' => FT::SEQ_OPTIONAL_MANUAL(
            FT::ACTION(DISCARD, []),
            FT::ACTION(INVOKE_TOKEN, [
                'pId' => 'source',
                'tokenType' => 'OD_Common_OrdisRecruit',
                'targetLocation' => ['discardedSource'],
            ])
          )
      ])
    ];
  }
}
