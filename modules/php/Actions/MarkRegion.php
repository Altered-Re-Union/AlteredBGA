<?php

namespace ALT\Actions;

use ALT\Managers\Meeples;
use ALT\Managers\Players;
use ALT\Managers\Cards;
use ALT\Core\Globals;
use ALT\Core\Notifications;

class MarkRegion extends \ALT\Models\Action
{
  public function getState()
  {
    return ST_MARK_REGION;
  }

  public function getDescription()
  {
    if ($this->getArg('create') === true) {
      $regionTypes = $this->getArg('regionTypes');
      if (!empty($regionTypes)) {
        return clienttranslate('Place a {V}, {M}, or {O} Terrain Marker on target visible region.');
      }
      $descriptions = [
        FOREST => clienttranslate('Place a {V} Terrain Marker on target visible region.'),
        MOUNTAIN => clienttranslate('Place a {M} Terrain Marker on target visible region.'),
        OCEAN => clienttranslate('Place a {O} Terrain Marker on target visible region.'),
      ];
      $regionType = $this->getArg('regionType');
      if (isset($descriptions[$regionType])) {
        return ['log' => $descriptions[$regionType], 'args' => []];
      }
    }
    return clienttranslate('Mark a visible region');
  }

  protected $args = [
    'create' => false,
    'regionType' => null,
    'regionTypes' => [],
  ];

  public function argsMarkRegion()
  {
    $args = [
      'regions' => Globals::getVisibleRegions(),
      'markers' => $this->getMarkers(),
    ];

    if ($this->getArg('create') === true) {
      $regionTypes = $this->getArg('regionTypes');
      $regionType = $this->getArg('regionType');
      $args['create'] = true;
      if (!empty($regionTypes)) {
        $args['regionTypes'] = array_values($regionTypes);
        $args['descSuffix'] = 'createchoice';
      } elseif ($regionType !== null) {
        $args['regionType'] = $regionType;
        $args['descSuffix'] = 'create';
      } else {
        $args['descSuffix'] = 'create';
      }
    }

    return $args;
  }

  public function getMarkers()
  {
    $source = Cards::get($this->getSourceId());
    return Meeples::getOfType('card-' . $source->getId(), [OCEAN, FOREST, MOUNTAIN]);
  }

  public function isDoable($player)
  {
    return count($this->getMarkers()) > 0 || $this->getArg('create') === true;
  }

  public function actMarkRegion($markerId, $stormId)
  {
    $args = $this->argsMarkRegion();
    if ($this->getArg('create') === true) {
      $type = $this->resolveCreatedMarkerType($markerId);
      $marker = Meeples::singleCreate([
        'type' => $type,
        'location' => 'card-' . $this->getSourceId(),
        'player_id' => Players::getActiveId(),
      ]);
      $markerId = $marker->getId();
    } elseif (!isset($args['markers'][$markerId])) {
      throw new \BgaVisibleSystemException('Invalid terrain marker. Should not happen');
    }

    // TODO : manage tiebreaker

    // move the marker
    $marker = Meeples::get($markerId);
    $marker->setLocation('storm-' . $stormId);
    $marker->setState(Meeples::getNextPlayedMarker());

    // Notify
    Notifications::setTerrainMarker(Players::getActive(), Meeples::get($markerId), $this->getSource());
    $this->resolveAction([$markerId, $stormId]);
  }

  protected function resolveCreatedMarkerType($markerId)
  {
    $allowed = $this->getArg('regionTypes');
    $single = $this->getArg('regionType');
    $valid = [FOREST, MOUNTAIN, OCEAN];

    if (!empty($allowed)) {
      if (!in_array($markerId, $allowed, true)) {
        throw new \BgaVisibleSystemException('Invalid terrain marker type. Should not happen');
      }
      return $markerId;
    }

    if ($single !== null) {
      if (!in_array($single, $valid, true)) {
        throw new \BgaVisibleSystemException('Invalid terrain marker type. Should not happen');
      }
      return $single;
    }

    if (in_array($markerId, $valid, true)) {
      return $markerId;
    }

    throw new \BgaVisibleSystemException('Invalid terrain marker. Should not happen');
  }
}
