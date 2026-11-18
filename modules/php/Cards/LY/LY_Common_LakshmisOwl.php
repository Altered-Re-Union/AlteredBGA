<?php

namespace ALT\Cards\LY;

use ALT\Helpers\FT;

class LY_Common_LakshmisOwl extends \ALT\Models\Card
{
    public function __construct($row)
    {
        parent::__construct($row);
        $this->properties = [
            'uid' => 'ALT_EOLE_B_LY_119_C',
            'asset'  => 'ALT_EOLE_B_LY_119_C',

            'faction'  => FACTION_LY,
            'rarity'  => RARITY_COMMON,
            'name'  => clienttranslate("Lakshmi's Owl"),
            'typeline' => clienttranslate("Character - Animal, Spirit"),
            'type'  => CHARACTER,
            'flavorText'  => clienttranslate('Even in the blackest night, I can see light.'),
            'artist' => "Fahmi Fauzi",
            'extension' => 'ROC',
            'subtypes'  => [ANIMAL, SPIRIT],
            'effectDesc' => clienttranslate('When you roll one or more dice — If I have no boosts, you may give me 1 boost and <FLEETING_CHAR> .'),
            'forest' => 0,
            'mountain' => 1,
            'ocean' => 0,
            'costHand' => 1,
            'costReserve' => 1,
            'effectPassive' => [
                'RollDie' => [
                    'conditions' => ['isMe', 'hasBoost:0:LTE', 'isInStorms'],
                    'output' => FT::SEQ_OPTIONAL(FT::GAIN(ME, BOOST), FT::GAIN(ME, FLEETING)),
                ],
            ]
        ];
    }
}
