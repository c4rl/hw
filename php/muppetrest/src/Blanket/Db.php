<?php

namespace Blanket;

/**
 * Class Db.
 *
 * Used for accessing PDO-based storage.
 *
 * @package Blanket
 */
class Db {

  /**
   * Wrapped PDO instance.
   *
   * @var \PDO
   */
  private $pdo;

  /**
   * Cached registry of schema for applicable models.
   *
   * @var array
   */
  private $schema_registry = [];

  /**
   * Db constructor. Works mostly with sqlite for now.
   *
   * @param string $dsn
   *   Data source name.
   */
  public function __construct($dsn) {
    $this->pdo = new \PDO($dsn);
    // We want exceptions thrown pls.
    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }

  /**
   * Returns schema array for given table name.
   *
   * @param string $table
   *   Name of table.
   *
   * @return array
   *   Schema array keyed by field with 'name' and 'type'.
   */
  public function getSchema($table) {
    return $this->schema_registry[$table];
  }

  /**
   * Registers schema of given model class name.
   *
   * @param string $model_class_name
   *   FQCN of model class.
   *
   * @return array
   *   Schema array keyed by field with 'name' and 'type'.
   */
  public function registerSchema($model_class_name) {

    /** @var Model $model_class_name */
    $cache_key = $model_class_name::getTable();

    if (!array_key_exists($cache_key, $this->schema_registry)) {
      $reflection = new \ReflectionClass($model_class_name);
      $lines = explode(PHP_EOL, $reflection->getDocComment());

      $schema = array_reduce($lines, function (array $schema, $line) {

        $matches = [];
        if (preg_match('/^\* @property ([^ ]+) ([^ ]+) ?.*$/', trim($line), $matches)) {
          $name = preg_replace('/[^a-z]/i', '', $matches[2]);
          $schema[$name] = [
            'name' => $name,
            'type' => $matches[1] == 'int' ? \PDO::PARAM_INT : \PDO::PARAM_STR,
          ];
        }

        return $schema;
      }, []);

      $this->schema_registry[$cache_key] = $schema;
    }

    return $this->schema_registry[$cache_key];
  }

  /**
   * Performs static query.
   *
   * @param string $sql
   *   Raw SQL.
   *
   * @return \PDOStatement
   *   Resulting statement.
   */
  public function query($sql) {
    return $this->pdo->query($sql);
  }

  /**
   * Prepares SQL for execution with placeholders.
   *
   * @param string $sql
   *   SQL with placeholders.
   *
   * @return \PDOStatement
   *   Resulting statement.
   */
  public function prepare($sql) {
    return $this->pdo->prepare($sql);
  }

  /**
   * Last inserted ID from most recent query.
   *
   * @return int
   */
  public function lastInsertId() {
    return $this->pdo->lastInsertId();
  }

  /**
   * Factory for INSERT statement query.
   *
   * @param string $table
   *   Name of table to query.
   *
   * @return DbInsertStatement
   *   Insert statement.
   */
  public function insert($table) {
    return new DbInsertStatement($table, $this);
  }

  /**
   * Factory for UPDATE statement query.
   *
   * @param string $table
   *   Name of table to query.
   *
   * @return DbUpdateStatement
   *   Update statement.
   */
  public function update($table) {
    return new DbUpdateStatement($table, $this);
  }

  /**
   * Factory for DELETE statement query.
   *
   * @param string $table
   *   Name of table to query.
   *
   * @return DbDeleteStatement
   *   Delete statement.
   */
  public function delete($table) {
    return new DbDeleteStatement($table, $this);
  }

  /**
   * Factory for SELECT statement query.
   *
   * @param string $table
   *   Name of table to query.
   *
   * @return DbSelectStatement
   *   Select statement.
   */
  public function select($table) {
    return new DbSelectStatement($table, $this);
  }

}
