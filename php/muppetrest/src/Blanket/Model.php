<?php

namespace Blanket;

class Model {

  protected $original_attributes = [];
  protected $attributes = [];

  protected static $table;

  public function __construct(array $attributes = []) {
    $this->original_attributes = $this->attributes = $attributes;
  }

  public static function findOrFail($id) {
    $db = static::db();
    /** @var \PDOStatement $result */
    $result = $db->query(sprintf('SELECT * FROM %s WHERE id = %d LIMIT 0, 1', static::$table, $id));
    if ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
      $instance = new static($row);
      return $instance;
    }
    throw new RecordNotFoundException();
  }

  public static function db() {
    $pdo = new \PDO(sprintf('sqlite:%s/storage/db.sqlite', WEBROOT));
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $pdo;
  }

  public static function filterStringKeys(array $row) {
    return array_filter($row, function ($value, $key) {
      return !is_numeric($key);
    }, ARRAY_FILTER_USE_BOTH);
  }

  public static function all($page = 1, $per_page = 10) {

    $start = ($page - 1) * $per_page;

    $result = static::db()
      ->query(sprintf('SELECT * FROM %s LIMIT %d, %d', static::$table, $start, $per_page));

    $instances = [];
    while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
      $instances[] = new static($row);
    }

    return $instances;
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

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  public function __isset($name) {
    return array_key_exists($name, $this->attributes);
  }

  public function update(array $attributes) {
    foreach ($attributes as $key => $value) {
      $this->$key = $value;
    }
    return $this;
  }

  public function getAttributes() {
    return $this->attributes;
  }

  public function hasChanged() {
    return $this->attributes != $this->original_attributes;
  }

  public function save() {
    throw new \Exception();
  }

  public function saveIfChanged() {
    if ($this->hasChanged()) {
      $this->save();
    }
    return $this;
  }

  public function delete() {
    $db = static::db();
    if (isset($this->id)) {
      $db->query(sprintf('DELETE FROM %s WHERE id = %d', static::$table, $this->id));
      return $this;
    }
    else {
      throw new \LogicException();
    }

  }

}