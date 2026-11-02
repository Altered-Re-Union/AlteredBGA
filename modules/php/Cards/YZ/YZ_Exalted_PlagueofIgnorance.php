<?php
namespace ALT\Cards\YZ;
use ALT\Helpers\FT;

class YZ_Exalted_PlagueofIgnorance extends \ALT\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->properties = [
      'uid' => 'ALT_EOLE_B_YZ_116_E',
      'asset'  => 'ALT_EOLE_B_YZ_116_E',

      'faction'  => FACTION_YZ,
      'rarity'  => RARITY_EXALTED,
      'name'  => clienttranslate("Plague of Ignorance"),
      'typeline' => clienttranslate("Character - Corruption"),
      'type'  => CHARACTER,
      'flavorText'  => clienttranslate('Ignorance is the crucible of evil.'),
      'artist' => "Taras Susak",
      'extension' => 'ROC',
      'subtypes'  => [CORRUPTION],
      'effectDesc' => clienttranslate('{H} You may discard a card from your hand to send target Character to Reserve.'),
      'forest' => 4,
      'mountain' => 4,
      'ocean' => 3,
      'costHand' => 4,
      'costReserve' => 3,
      'effectHand' => FT::ACTION(CHECK_CONDITION, [
        'condition' => 'hasCardsInHand',
        'effect' => FT::SEQ_OPTIONAL(
          FT::ACTION(DISCARD, ['source' => HAND]),
          FT::ACTION(TARGET, ['effect' => FT::DISCARD_TO_RESERVE()])
        ),
      ]),
    ];
  }
}
