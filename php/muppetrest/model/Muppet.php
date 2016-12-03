<?php

namespace Muppet;

class Muppet {

  public static function all() {

    $pdo = new \PDO(sprintf('sqlite:%s/storage/db.sqlite', WEBROOT));

    $page = 1;
    $per_page = 10;

    $result = $pdo->query(sprintf('SELECT rowid,* FROM muppets LIMIT %d, %d', $page - 1, $per_page));

    $total = 0;
    $muppets = [];
    foreach ($result as $row) {
      $total++;
      $muppets[] = filter_string_keys($row);
    }

    return compact('total', 'page', 'per_page', 'muppets');
  }

}
