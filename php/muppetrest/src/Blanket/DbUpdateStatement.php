<?php

namespace Blanket;

/**
 * Class DbUpdateStatement.
 *
 * @package Blanket
 */
class DbUpdateStatement extends DbStatement {

  /**
   * Conditions array.
   *
   * @var string[]
   */
  private $conditions = [];

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
   * @return $this
   *   Self.
   */
  public function condition($key, $value, $operator = '=') {
    $data = $this->addPlaceholder($key, $value);

    $this->conditions[] = sprintf('(%s %s %s)', $data['name'], $operator, $data['placeholder']);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function execute() {

    $fields_pieces = [];
    foreach ($this->fields as $key => $value) {
      $data = $this->addPlaceholder($key, $value);
      $fields_pieces[] = sprintf('%s = %s', $data['name'], $data['placeholder']);
    }

    $fields_string = implode(', ', $fields_pieces);

    $conditions_string = count($this->conditions) > 0 ? sprintf('WHERE %s', implode(' AND ', $this->conditions)) : '';

    $sql = trim(strtr('UPDATE ___TABLE___ SET ___FIELDS___ ___CONDITIONS___', [
      '___TABLE___' => $this->table,
      '___FIELDS___' => $fields_string,
      '___CONDITIONS___' => $conditions_string,
    ]));

    return $this->prepare($sql)->bindPlaceholders()->statement->execute();
  }

}