<?php
// Single deck of the "Roots of Corruption" tournament format (deck format 'DEMO').
const DEMO_ROC_TOURNAMENT = [
  'MU_Common_KauriPuff' => 1,
  'MU_Rare_HerdingPuli' => 2,
  'MU_Rare_BraveCub' => 2,
  'MU_Rare_WhiteFang' => 2,
  'MU_Common_NikauKaurisFather' => 2,
  'MU_Rare_Pan' => 2,
  'MU_Rare_EruEmissaryoftheWild' => 2,
  'MU_Common_ColorfulCuckoo' => 2,
  'MU_Common_ZaratanSocialLeviathan' => 2,
  'MU_Common_CountingSheep' => 2,
  'MU_Rare_WoollyWoollyField' => 2,
  'MU_Rare_Bait' => 2,
  'MU_Rare_Caladrius' => 2,
  'MU_Rare_SapDuende' => 2,
  'MU_Rare_Pasiphae' => 2,
  'MU_Rare_SaskiaSlyNaturalist' => 2,
  'MU_Rare_Arawn' => 2,
  'MU_Common_ResentfulRobin' => 2,
  'MU_Exalted_PlagueofPrejudice' => 2,
];

// Uniques of the deck, in the shape expected by Cards::generateUnique().
// Effects ({trigger, condition, output} ids) come from CardsData UniquePrintEffectFragments.csv.
const DEMO_ROC_TOURNAMENT_UNIQUES = [
  [
    'reference' => 'ALT_EOLE_B_MU_108_U_5014',
    'mainFaction' => ['reference' => 'MU'],
    'name' => 'Corrupted Woollyback',
    'cardType' => ['reference' => 'CHARACTER'],
    'subTypes' => ['CORRUPTION', 'ANIMAL'],
    'typeline' => 'Character - Corruption Animal',
    'illustrator' => 'Zaeliven',
    'elements' => ['MAIN_COST' => 1, 'RECALL_COST' => 1, 'FOREST_POWER' => 1, 'MOUNTAIN_POWER' => 1, 'OCEAN_POWER' => 0],
    'uniqueReduced' => [
      ['effects' => [24, 894, 918]],
      ['effects' => [24, 191, 558]],
    ],
  ],
  [
    'reference' => 'ALT_EOLE_B_MU_108_U_399',
    'mainFaction' => ['reference' => 'MU'],
    'name' => 'Corrupted Woollyback',
    'cardType' => ['reference' => 'CHARACTER'],
    'subTypes' => ['CORRUPTION', 'ANIMAL'],
    'typeline' => 'Character - Corruption Animal',
    'illustrator' => 'Zaeliven',
    'elements' => ['MAIN_COST' => 1, 'RECALL_COST' => 1, 'FOREST_POWER' => 1, 'MOUNTAIN_POWER' => 1, 'OCEAN_POWER' => 0],
    'uniqueReduced' => [
      ['effects' => [24, 634, 822]],
      ['effects' => [24, 191, 558]],
      ['effects' => [192, 191, 742]],
    ],
  ],
  [
    'reference' => 'ALT_EOLE_B_MU_108_U_3368',
    'mainFaction' => ['reference' => 'MU'],
    'name' => 'Corrupted Woollyback',
    'cardType' => ['reference' => 'CHARACTER'],
    'subTypes' => ['CORRUPTION', 'ANIMAL'],
    'typeline' => 'Character - Corruption Animal',
    'illustrator' => 'Zaeliven',
    'elements' => ['MAIN_COST' => 1, 'RECALL_COST' => 1, 'FOREST_POWER' => 2, 'MOUNTAIN_POWER' => 0, 'OCEAN_POWER' => 0],
    'uniqueReduced' => [
      ['effects' => [24, 634, 929]],
      ['effects' => [24, 191, 558]],
      ['effects' => [192, 191, 872]],
    ],
  ],
];
?>
