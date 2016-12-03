<?php

require 'vendor/autoload.php';

define('WEBROOT', __DIR__);

main();

function main() {

  header('Content-Type: application/json');

  switch (route()) {

    case 'muppets':

      $content = Muppet\Muppet::all();
      
      break;

    default:
      $content = NULL;
  }

  print json_encode($content, JSON_PRETTY_PRINT);

}

function qs() {
  return $_GET;
}

function route() {
  return trim(parse_url($_SERVER['REQUEST_URI'])['path'], '/');
}

function filter_string_keys(array $row) {
  return array_filter($row, function ($value, $key) {
    return !is_numeric($key);
  }, ARRAY_FILTER_USE_BOTH);
}