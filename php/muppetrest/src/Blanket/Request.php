<?php

namespace Blanket;

/**
 * Class Request.
 *
 * @package Blanket
 */
class Request {

  /**
   * Path of request.
   *
   * @var string.
   */
  public $path;

  /**
   * HTTP method, lowercase.
   *
   * @var string
   */
  public $method;

  /**
   * Key-values from POST request.
   *
   * @var array
   */
  public $post_data;

  /**
   * Key-values from GET request.
   *
   * @var array
   */
  public $get_data;

  /**
   * Key-values from PUT request.
   *
   * @var array
   */
  public $put_data;

  /**
   * Protocol of server, $_SERVER['SERVER_PROTOCOL'].
   *
   * @var string
   */
  public $protocol;

  /**
   * Reads raw JSON from PUT data into array.
   *
   * @return array
   *   Key-value of PUT data.
   */
  private static function readPutData() {
    $h = fopen('php://input', 'r');
    $json = '';
    while ($chunk = fread($h, 1024)) {
      $json .= $chunk;
    }
    fclose($h);

    return json_decode($json, TRUE);
  }

  /**
   * @param array $globals
   *   Optional globals for construction.
   *
   * @return Request
   *   Created instance.
   */
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
