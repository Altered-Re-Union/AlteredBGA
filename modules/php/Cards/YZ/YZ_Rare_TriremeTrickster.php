<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Rare_TriremeTrickster extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_FUGUE_B_LY_147_R2',
      'asset' => 'ALT_FUGUE_B_LY_147_R',
      'faction' => FACTION_YZ,
      'rarity' => RARITY_RARE,
      'name' => clienttranslate('Trireme Trickster'),
      'typeline' => clienttranslate('Character - Artist'),
      'type' => CHARACTER,
      'artist' => 'Victor Canton',
      'extension' => 'NEJ',
      'subtypes' => [ARTIST],
      'effectDesc' => clienttranslate('When you #draw one or more cards — I gain 1 boost#.'),
      'forest' => 2,
      'mountain' => 1,
      'ocean' => 1,
      'costHand' => 2,
      'costReserve' => 2,
      'effectPassive' => [
          'Draw' => [
              'conditions' => ['isMyDraw', 'isInStorms'],
              'output' => FT::GAIN(ME, BOOST),
          ],
          // Morning draws don't go through the Draw action
          'Morning' => [
              'conditions' => ['isMe', 'isInStorms'],
              'output' => FT::GAIN(ME, BOOST),
          ],
      ],
    ];
  }
}
