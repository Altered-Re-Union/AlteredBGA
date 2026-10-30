<?php
namespace ALT\Cards\LY;
use ALT\Helpers\FT;

class LY_Rare_TriremeTrickster extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_147_R1',
      'asset' => 'ALT_FUGUE_B_LY_147_R',
      'faction' => FACTION_LY,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Trireme Trickster'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('When you roll one or more dice — #I gain 1 boost#.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPassive' => [
          'RollDie' => [
              'conditions' => ['isMe', 'isInStorms'],
              'output' => FT::GAIN(ME, BOOST),
          ],
      ],
    ];
  }
}
