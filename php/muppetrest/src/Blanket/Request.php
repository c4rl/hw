<?php

namespace Blanket;

class Request {

  public $path;

  public $method;

  public $params;

  public $protocol;

  public static function createFromGlobals($globals = NULL) {
    if (!isset($globals)) {
      $globals = $_SERVER + [
        '_REQUEST_DATA' => $_POST,
      ];
    }

    $instance = new self();
    $instance->method = strtolower($globals['REQUEST_METHOD']);
    $instance->path = trim(parse_url($globals['REQUEST_URI'])['path'], '/');
    $instance->params = $globals['_REQUEST_DATA'];
    $instance->protocol = $globals['SERVER_PROTOCOL'];

    return $instance;
  }
}
