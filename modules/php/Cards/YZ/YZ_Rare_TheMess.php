<?php

namespace ALT\Cards\YZ;

use ALT\Helpers\FT;

class YZ_Rare_TheMess extends \ALT\Models\Card
{
    public function __construct($row)
    {
        parent::__construct($row);
        $this->properties = [
            'uid' => 'ALT_BISE_B_BR_63_R2',
            'asset'  => 'ALT_BISE_B_BR_63_R',

            'faction'  => FACTION_YZ,
            'rarity'  => RARITY_RARE,
            'name'  => clienttranslate("The Mess"),
            'typeline' => clienttranslate("Landmark Permanent - Construction"),
            'type'  => PERMANENT,
            'flavorText'  => clienttranslate('Every evening, the Mess overflows with laughter and shouting as explorers share their daily exploits.'),
            'artist' => "Fahmi Fauzi",
            'extension' => 'WFTM',
            'subtypes'  => [CONSTRUCTION, LANDMARK],
            'effectDesc' => clienttranslate('When a #non-token# Character leaves your Expeditions — I gain 1 Food counter.  {T}, #Spend 4# of my Food counters: #draw a card.#'),
            'costHand' => 2,
            'costReserve' => 2,
            'effectPassive' => [
                'OtherLeaveExpedition' => [
                    'pId' => CONTROLLER,
                    'conditions' => ['hasSameEventController', 'isDiscardedType:characterOnly'],
                    'output' => FT::ACTION(SPECIAL_EFFECT, [
                        'effect' => 'incCounter',
                        'args' => ['counter' => 1, 'counterName' => clienttranslate('Food counter')],
                    ]),
                ],
            ],
            'effectTap' => FT::ACTION(
                CHECK_CONDITION,
                [
                    'condition' => 'hasCounterOnCard:4',
                    'effect' => FT::SEQ(
                        FT::ACTION(USE_COUNTER, ['consume' => 4], ['sourceId' => $this->id]),
                        FT::ACTION(DRAW, ['players' => ME])
                    )
                ],
                ['sourceId' => $this->id]
            )

        ];
    }
}
