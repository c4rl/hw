<?php

namespace Blanket;

/**
 * Class DbStatement.
 *
 * @package Blanket
 */
abstract class DbStatement {

  /**
   * Storage mechanism.
   *
   * @var Db
   */
  protected $db;

  /**
   * Name of primary table on which to execute statement.
   *
   * @var string
   */
  protected $table;

  /**
   * Applicable fields for statement.
   *
   * @var array
   */
  protected $fields = [];

  /**
   * Registered placeholders for SQL statement.
   *
   * @var array
   */
  protected $placeholders = [];

  /**
   * Wrapped PDOStatement.
   *
   * @var \PDOStatement
   */
  protected $statement;
  /**
   * Conditions array.
   *
   * @var string[]
   */
  protected $conditions = [];

  /**
   * DbStatement constructor.
   *
   * @param string $table
   *   Name of primary table on which to execute statement.
   * @param Db $db
   *   Storage mechanism.
   */
  public function __construct($table, Db $db) {
    $this->table = $table;
    $this->db = $db;
  }

  /**
   * Provides a condition for the SQL statement.
   *
   * @param string $key
   *   Name of column.
   * @param mixed $value
   *   Value to match.
   * @param string $operator
   *   Comparison operator, defaults to '<'.
   *
   * @return DbUpdateStatement
   *   Self.
   */
  public function condition($key, $value, $operator = '=') {
    $data = $this->addPlaceholder($key, $value);

    $this->conditions[] = sprintf('(%s %s %s)', $data['name'], $operator, $data['placeholder']);

    return $this;
  }

  /**
   * Returns schema array for table.
   *
   * @return array
   *   Schema array keyed by field with 'name' and 'type'.
   */
  public function getSchema() {
    return $this->db->getSchema($this->table);
  }

  /**
   * Fluent setter for fields applicable to query.
   *
   * @param array $fields
   *   Key-value of fields.
   *
   * @return $this
   *   Self.
   */
  public function fields(array $fields) {
    $this->fields = $fields;
    return $this;
  }

  /**
   * Setter for adding a placeholder for usage in statement.
   *
   * @param string $key
   *   Name of column.
   * @param mixed $value
   *   Value to be bound.
   *
   * @return array
   *   Placeholder data with 'placeholder', 'name', 'value'.
   */
  protected function addPlaceholder($key, $value) {
    $placeholder = sprintf(':placeholder_%d', count($this->placeholders));
    $data = [
      'placeholder' => $placeholder,
      'name' => $key,
      'value' => $value,
    ];
    $this->placeholders[$placeholder] = $data;

    return $data;
  }

  /**
   * Getter for all placeholders.
   *
   * @return array
   *   Array of placeholder data.
   */
  protected function getPlaceholders() {
    return $this->placeholders;
  }

  /**
   * Binds set placeholders to wrapped statement.
   *
   * @return $this
   *   Self.
   */
  protected function bindPlaceholders() {
    $schema = $this->getSchema();
    foreach ($this->placeholders as $placeholder => $data) {
      $type = $schema[$data['name']]['type'];
      $this->statement->bindValue($placeholder, $data['value'], $type);
    }
    return $this;
  }

  /**
   * Prepares wrapped statement from SQL string.
   *
   * @param string $sql
   *   SQL string with placeholder text.
   *
   * @return $this
   *   Self.
   */
  protected function prepare($sql) {
    $this->statement = $this->db->prepare($sql);
    return $this;
  }

  /**
   * Executes statement.
   *
   * @return bool
   *   TRUE on success, FALSE otherwise.
   */
  abstract public function execute();

  /**
   * Get full conditions string.
   *
   * @return string
   *   Full SQL conditions string.
   */
  protected function getConditionsString() {
    return count($this->conditions) > 0 ? sprintf('WHERE %s', implode(' AND ', $this->conditions)) : '';
  }

}