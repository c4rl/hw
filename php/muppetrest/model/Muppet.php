<?php

namespace Muppet;

/**
 * @property mixed occupation
 * @property mixed name
 * @property mixed id
 */
class Muppet {

  protected $attributes = [];

  protected static $table = 'muppets';

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

    $result = static::db()->query(sprintf('SELECT * FROM %s LIMIT %d, %d', static::$table, $page - 1, $per_page));

    $total = 0;
    $records = [];
    foreach ($result as $row) {
      $total++;
      $records[] = filter_string_keys($row);
    }

    return compact('total', 'page', 'per_page') + [
      static::$table => $records,
    ];
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
      $db->query(sprintf('UPDATE %s SET name = "%s", occupation = "%s"', static::$table, $this->name, $this->occupation));
    }
    else {
      $db->query(sprintf('INSERT INTO %s (name, occupation) VALUES ("%s", "%s")', static::$table, $this->name, $this->occupation));
      $this->id = $db->lastInsertId();
    }
  }

  public function getAttributes() {
    return $this->attributes;
  }

}
