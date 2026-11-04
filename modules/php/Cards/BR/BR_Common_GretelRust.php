<?php
namespace ALT\Cards\BR;

class BR_Common_GretelRust extends \ALT\Models\Card
{
	public function __construct($row)
	{
		parent::__construct($row);
		$this->properties = [
			'uid' => 'ALT_EOLE_B_BR_105_C',
			'asset' => 'ALT_EOLE_B_BR_105_C',

			'faction' => FACTION_BR,
			'rarity' => RARITY_COMMON,
			'name' => clienttranslate('Gretel & Rust'),
			'type' => HERO,
			'thumbnail' => 4,
			'statData' => 29,
			'typeline' => clienttranslate('Bravos Hero'),
			'effectDesc' => clienttranslate('(Keep up to three cards in your Landmarks during Night.)  At Noon — If you control a Feat, create a Rust 0/0/0 token in your Companion Expedition.(It\'s a Companion with \"{j} I gain 1 boost per Completed Feat in your Landmarks\".)'),
			'flavorText'  => clienttranslate('"If you underestimate me, my victory will be all the sweeter."'),
			'artist' => 'Tristan Bideau',
			'reserveSlots' => 2,
			'landmarkSlots' => 3,
			'effectPassive' => [
				'Noon' => [
					'condition' => 'hasControl:feat:1',
					'output' => [
						'action' => INVOKE_TOKEN,
						'automatic' => true,
						'args' => ['tokenType' => 'BR_Common_Rust', 'targetLocation' => [STORM_RIGHT]],
					],
				],
			],
		];
	}
}
