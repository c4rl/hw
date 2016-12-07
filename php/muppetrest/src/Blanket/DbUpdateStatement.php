<?php

namespace Blanket;

/**
 * Class DbUpdateStatement.
 *
 * @package Blanket
 */
class DbUpdateStatement extends DbStatement {

  /**
   * {@inheritdoc}
   */
  protected function getSqlStatement() {

    $fields_pieces = [];
    foreach ($this->fields as $key => $value) {
      $data = $this->addPlaceholder($key, $value);
      $fields_pieces[] = sprintf('%s = %s', $data['name'], $data['placeholder']);
    }

    $fields_string = implode(', ', $fields_pieces);

    return trim(strtr('UPDATE ___TABLE___ SET ___FIELDS___ ___CONDITIONS___', [
      '___TABLE___' => $this->table,
      '___FIELDS___' => $fields_string,
      '___CONDITIONS___' => $this->getConditionsString(),
    ]));
  }

}