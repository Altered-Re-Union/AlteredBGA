<?php

namespace ALT\Models;

/*
 * Meeple
 */

class Meeple extends \ALT\Helpers\DB_Model
{
  protected $table = 'meeples';
  protected $primary = 'meeple_id';
  protected $attributes = [
    'id' => ['meeple_id', 'int'],
    'location' => 'meeple_location',
    'state' => 'meeple_state',
    'type' => 'type',
    'pId' => 'player_id',
  ];

  // Explicit getters for the fields read on every meeple when collections are filtered: same
  // result as the magic getter, without going through it
  public function getId()
  {
    return $this->id;
  }

  public function getLocation()
  {
    return $this->location;
  }

  public function getState()
  {
    return $this->state;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getPId()
  {
    return $this->pId;
  }

  public function getLocationArg()
  {
    return explode('-', $this->getLocation())[1];
  }
}
