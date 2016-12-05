<?php

namespace Blanket;

class Request {

  public $path;

  public $method;

  public $post_data;
  public $get_data;
  public $put_data;

  public $protocol;

  public static function readPutData() {
    $h = fopen('php://input', 'r');
    $json = '';
    while ($chunk = fread($h, 1024)) {
      $json .= $chunk;
    }
    fclose($h);

    return json_decode($json, TRUE);
  }

  public static function createFromGlobals($globals = NULL) {
    if (!isset($globals)) {
      $globals = $_SERVER;
      $globals['_POST_DATA'] = $_POST;
      $globals['_GET_DATA'] = $_GET;
      $globals['_PUT_DATA'] = $globals['REQUEST_METHOD'] == 'PUT' ? self::readPutData() : NULL;
    }

    $instance = new self();
    $instance->method = strtolower($globals['REQUEST_METHOD']);
    $instance->path = trim(parse_url($globals['REQUEST_URI'])['path'], '/');
    $instance->post_data = $globals['_POST_DATA'];
    $instance->get_data = $globals['_GET_DATA'];
    $instance->put_data = $globals['_PUT_DATA'];
    $instance->protocol = $globals['SERVER_PROTOCOL'];

    return $instance;
  }
}
