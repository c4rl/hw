<?php

namespace Blanket;

use Blanket\Exception\RecordNotFoundException;

/**
 * Class Model.
 *
 * @package Blanket
 */
class Model {

  /**
   * Array of attributes.
   *
   * @var array
   */
  protected $attributes = [];

  /**
   * Array of attributes at time of construction.
   *
   * @var array
   */
  protected $original_attributes = [];

  /**
   * Name of table in storage.
   *
   * @var string
   */
  protected static $table;

  /**
   * Storage mechanism.
   *
   * @var Db
   */
  public static $storage;

  /**
   * Model constructor.
   *
   * @param array $attributes
   *   Attributes.
   */
  public function __construct(array $attributes = []) {
    $this->original_attributes = $this->attributes = $attributes;
  }

  /**
   * Returns table name for model storage.
   *
   * @return string
   *   Table name.
   */
  public static function getTable() {
    return static::$table;
  }

  /**
   * Getter for attribute.
   *
   * @param string $name
   *   Name of attribute.
   *
   * @return mixed
   *   Attribute value.
   */
  public function __get($name) {
    return $this->attributes[$name];
  }

  /**
   * Setter for attribute.
   *
   * @param string $name
   *   Name of attribute.
   *
   * @param mixed $value
   *   Attribute value to set.
   */
  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  /**
   * Existence for attribute.
   *
   * @param string $name
   *
   * @return bool
   *   Whether value exists.
   */
  public function __isset($name) {
    return array_key_exists($name, $this->attributes);
  }

  /**
   * Getter for attributes.
   *
   * @return array
   *   Attributes.
   */
  public function getAttributes() {
    return $this->attributes;
  }

  /**
   * Updates attributes.
   *
   * @param array $attributes
   *   Attributes to update.
   *
   * @return $this
   *   Self.
   */
  public function updateAttributes(array $attributes) {
    foreach ($attributes as $key => $value) {
      $this->{$key} = $value;
    }

    return $this;
  }

  /**
   * Whether the model has been altered since construction.
   *
   * @return bool
   *   TRUE if model has changed, FALSE otherwise.
   */
  public function hasChanged() {
    return $this->attributes != $this->original_attributes;
  }

  /**
   * Saves instance.
   *
   * @return $this
   *   Self.
   */
  public function save() {
    if (isset($this->id)) {
      static::$storage->update(static::$table)
        ->fields($this->getAttributes())
        ->condition('id', $this->id)
        ->execute();
    }
    else {
      static::$storage->insert(static::$table)
        ->fields($this->getAttributes())
        ->execute();
      // Update id based on this insert.
      $this->id = static::$storage->lastInsertId();
    }

    return $this;
  }

  /**
   * Saves instance if indeed it has changed.
   *
   * @return $this
   *   Self.
   */
  public function saveIfChanged() {
    if ($this->hasChanged()) {
      $this->save();
    }

    return $this;
  }

  /**
   * Static method for instance creation.
   *
   * @param array $attributes
   *   Attributes.
   *
   * @return static
   *   New, saved instance.
   */
  public static function create(array $attributes) {
    return (new static($attributes))->save();
  }

  /**
   * Deletes instance from storage.
   *
   * @return $this
   *   Self.
   */
  public function delete() {
    if (!isset($this->id)) {
      throw new \LogicException();
    }

    static::$storage->delete(static::$table)
      ->condition('id', $this->id)
      ->execute();

    return $this;
  }

  /**
   * Finds record by id.
   *
   * @param int $id
   *   Primary key identifier.
   *
   * @return static
   *   Found instance.
   *
   * @throws RecordNotFoundException
   *   If record not found.
   */
  public static function findOrFail($id) {
    /** @var \PDOStatement $result */
    $result = static::$storage->query(sprintf('SELECT * FROM %s WHERE id = %d LIMIT 0, 1', static::$table, $id));
    if ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
      $instance = new static($row);
      return $instance;
    }
    throw new RecordNotFoundException();
  }

  /**
   * @param int $page
   *   Page of records. Defaults to 1.
   *
   * @param int $per_page
   *   Number of records per page. Defaults to 10.
   *
   * @return static[]
   *   Loaded instances.
   */
  public static function all($page = 1, $per_page = 10) {

    $start = ($page - 1) * $per_page;

    $result = static::$storage
      ->query(sprintf('SELECT * FROM %s LIMIT %d, %d', static::$table, $start, $per_page));

    $instances = [];
    while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
      $instances[] = new static($row);
    }

    return $instances;
  }

}