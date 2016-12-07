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
  public function execute() {

    $sql = trim(strtr('DELETE FROM ___TABLE___ ___CONDITIONS___', [
      '___TABLE___' => $this->table,
      '___CONDITIONS___' => $this->getConditionsString(),
    ]));

    $this->prepare($sql)->bindPlaceholders()->statement->execute();
  }

}