<?php

namespace Muppet;

use Blanket\Model;

/**
 * @property string occupation
 * @property string name
 * @property int id
 */
class Muppet extends Model {

  protected static $table = 'muppets';

  public function save() {
    $db = static::db();
    if (isset($this->id)) {
      $db->query(sprintf('UPDATE %s SET name = "%s", occupation = "%s" WHERE id = %d', static::$table, $this->name, $this->occupation, $this->id));
    }
    else {
      $db->query(sprintf('INSERT INTO %s (name, occupation) VALUES ("%s", "%s")', static::$table, $this->name, $this->occupation));
      $this->id = $db->lastInsertId();
    }
    return $this;
  }

}
