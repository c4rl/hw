<?php

namespace Blanket;

/**
 * Class DbDeleteStatement.
 *
 * @package Blanket
 */
class DbDeleteStatement extends DbStatement {

  /**
   * {@inheritdoc}
   */
  protected function getSqlStatement() {
    return trim(strtr('DELETE FROM ___TABLE___ ___CONDITIONS___', [
      '___TABLE___' => $this->table,
      '___CONDITIONS___' => $this->getConditionsString(),
    ]));
  }

}