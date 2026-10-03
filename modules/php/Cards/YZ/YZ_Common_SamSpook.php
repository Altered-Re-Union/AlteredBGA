<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Common_SamSpook extends \ALT\Models\Card
{
	public function __construct($row)
	{
		parent::__construct($row);
		$this->properties = [
			'uid' => 'ALT_EOLE_B_YZ_105_C',
			'asset' => 'ALT_EOLE_B_YZ_105_C',

			'faction' => FACTION_YZ,
			'rarity' => RARITY_COMMON,
			'name' => clienttranslate('Sam & Spook'),
			'type' => HERO,
			'thumbnail' => 4,
			'statData' => 17,
			'typeline' => clienttranslate('Yzmir Hero'),
			'effectDesc' => clienttranslate(
				'{T} : Draw a card, then discard a card from your hand. You can only activate this if you are first player. As soon as six cards or more are in your discard pile, I permanently gain: \"When you discard a card from your hand — Return it to your Reserve.\".'
			),
						'flavorText'  => clienttranslate('"Finally! I think we\'re going to have some fun!"'),
			'artist' => 'Zero Wen',

			'reserveSlots' => 2,
			'landmarkSlots' => 2,

			'effectTap' => FT::ACTION(CHECK_CONDITION, [
				'condition' => 'isFirstPlayer',
				'ignoreDeck' => true,
				'effect' => FT::SEQ(
					FT::ACTION(DRAW, ['players' => ME, 'n' => 1]),
					FT::ACTION(DISCARD, ['source' => HAND]),
				),
			]),
			'effectPassive' => [
				'Discard' => [
					'conditions' => ['isMe', 'isDiscarded:hand:discard', 'hasEverReachXDiscardedCards:7'],
					'output' => FT::ACTION(DISCARD, ['cardId' => EFFECT, 'destination' => RESERVE]),
				],
			],
		];
	}
}
