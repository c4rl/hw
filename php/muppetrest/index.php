<?php

require 'vendor/autoload.php';

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

  print json_encode($content);

}

function qs() {
  return $_GET;
}

function route() {
  return trim(parse_url($_SERVER['REQUEST_URI'])['path'], '/');
}
