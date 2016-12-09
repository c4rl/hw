<?php

namespace Blanket;

/**
 * Trait SchemaTrait.
 *
 * @package Blanket
 */
trait SchemaTrait {

  /**
   * Coerces types on record sets.
   *
   * @param array $records
   *   Record set.
   * @param $schema
   *   Schema definition.
   *
   * @return array
   *   Coerced record set.
   */
  public static function coerceRecords(array $records, array $schema) {
    return array_map(function (array $attributes) use ($schema) {
      return static::coerceAttributes($attributes, $schema);
    }, $records);
  }

  /**
   * Coerces attributes to specified types.
   *
   * @param array $attributes
   *   Attributes.
   * @param array $schema
   *   Schema definition.
   *
   * @return array
   *   Coerced attributes.
   */
  public static function coerceAttributes(array $attributes, array $schema) {
    $coerced_attributes = [];
    foreach ($attributes as $name => $value) {
      $coerced_attributes[$name] = self::coerceType($name, $value, $schema);
    }
    return $coerced_attributes;
  }

  /**
   * Coerces variable to specified type.
   *
   * @param string $name
   *   Name of column.
   * @param mixed $value
   *   Value of column.
   * @param array $schema
   *   Schema definition.
   *
   * @return int|string
   *   Coerced value.
   */
  public static function coerceType($name, $value, array $schema) {
    return $schema[$name]['type'] == 'int' ? (int) $value : (string) $value;
  }

}