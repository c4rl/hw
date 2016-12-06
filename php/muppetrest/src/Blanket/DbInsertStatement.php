<?php

namespace Blanket;

/**
 * Class DbInsertStatement.
 *
 * @package Blanket
 */
class DbInsertStatement extends DbStatement {

  /**
   * {@inheritdoc}
   */
  public function execute() {

    $column_names = implode(', ', array_keys($this->fields));

    foreach ($this->fields as $key => $value) {
      $this->addPlaceholder($key, $value);
    }

    $column_placeholders = implode(', ', array_keys($this->getPlaceholders()));

    $sql = trim(strtr('INSERT INTO ___TABLE___ (___COLUMN_NAMES___) VALUES (___VALUES___)', [
      '___TABLE___' => $this->table,
      '___COLUMN_NAMES___' => $column_names,
      '___VALUES___' => $column_placeholders,
    ]));

    return $this->prepare($sql)->bindPlaceholders()->statement->execute();
  }

}