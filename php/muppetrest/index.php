<?php

require 'vendor/autoload.php';

define('WEBROOT', __DIR__);

main();

function main() {

  header('Content-Type: application/json');

  switch (route()) {

    case 'muppets':

      switch (method()) {
        case 'get':
          $content = Muppet\Muppet::all();
          break;
        case 'post':
          return Muppet\Muppet::create(request_params())->getAttributes();
          break;
      }

      break;

    default:
      $content = NULL;
  }

  print json_encode($content, JSON_PRETTY_PRINT);

}

function qs() {
  return $_GET;
}

function request_params() {
  return $_REQUEST;
}

function method() {
  return strtolower($_SERVER['REQUEST_METHOD']);
}

function route() {
  return trim(parse_url($_SERVER['REQUEST_URI'])['path'], '/');
}

function filter_string_keys(array $row) {
  return array_filter($row, function ($value, $key) {
    return !is_numeric($key);
  }, ARRAY_FILTER_USE_BOTH);
}