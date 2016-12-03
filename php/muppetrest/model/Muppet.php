<?php

namespace Muppet;

/**
 * @property mixed occupation
 * @property mixed name
 * @property mixed id
 */
class Muppet {

  private $attributes = [];

  public function __construct(array $attributes = []) {
    $this->attributes = $attributes;
  }

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  public function __isset($name) {
    return array_key_exists($name, $this->attributes);
  }

  public static function db() {
    $pdo = new \PDO(sprintf('sqlite:%s/storage/db.sqlite', WEBROOT));
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
  }

  public static function all() {

    $page = 1;
    $per_page = 10;

    $result = static::db()->query(sprintf('SELECT rowid id,* FROM muppets LIMIT %d, %d', $page - 1, $per_page));

    $total = 0;
    $muppets = [];
    foreach ($result as $row) {
      $total++;
      $muppets[] = filter_string_keys($row);
    }

    return compact('total', 'page', 'per_page', 'muppets');
  }

  /**
   * @param array $request_params
   * @return static
   */
  public static function create(array $request_params) {
    $instance = new static($request_params);
    $instance->save();
    return $instance;
  }

  private function save() {
    $db = static::db();
    if (isset($this->id)) {
      $db->query(sprintf('UPDATE muppets SET name = "%s", occupation = "%s"', $this->name, $this->occupation));
    }
    else {
      $db->query(sprintf('INSERT INTO muppets (name, occupation) VALUES ("%s", "%s")', $this->name, $this->occupation));
      $this->id = $db->lastInsertId();
    }
  }

  public function getAttributes() {
    return $this->attributes;
  }

}
