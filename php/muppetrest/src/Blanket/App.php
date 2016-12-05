<?php

namespace Blanket;

/**
 * Class App
 *
 * @method get($path, \Closure $closure)
 * @method post($path, \Closure $closure)
 * @method put($path, \Closure $closure)
 * @method del($path, \Closure $closure)
 *
 * @package Blanket
 */
class App {

  private $config = [];

  private $route_registry = [];

  public function __construct(array $config = []) {
    $this->config = $config;
  }

  public function __call($method, array $arguments) {

    if (count($arguments) != 2) {
      throw new \ArgumentCountError();
    }

    list($path, $callback) = $arguments;

    if (!($callback instanceof \Closure)) {
      throw new \TypeError();
    }

    $this->route_registry[] = compact('method', 'path', 'callback');
  }

  public static function pathMaskToRegex($path_mask) {
    static $cache = [];

    if (!array_key_exists($path_mask, $cache)) {
      $regex = sprintf('/^%s$/', preg_replace('/\//', '\/', preg_replace('/:[a-z]+/', '(.+?)', $path_mask)));
    }
    else {
      $regex = $path_mask[$cache];
    }

    return $regex;
  }

  public static function pathMaskToNames($path_mask) {
    $pieces = explode('/', $path_mask);
    return array_reduce($pieces, function (array $names, $piece) {
      return $piece[0] == ':' ? array_merge($names, [
        str_replace(':', ':', $piece),
      ]) : $names;
    }, []);
  }

  public static function requestMatchesRegistrant(array $route_registrant, Request $request) {
    return $request->method == $route_registrant['method'] && preg_match(self::pathMaskToRegex($route_registrant['path']), $request->path);
  }

  public static function extractPathArguments(array $route_registrant, Request $request) {
    $matches = [];
    if (preg_match(self::pathMaskToRegex($route_registrant['path']), $request->path, $matches)) {
      array_shift($matches);
      return array_combine(self::pathMaskToNames($route_registrant['path']), $matches);
    }
    else {
      return [];
    }
  }

  public function getResponse(Request $request) {

    $matched_registrant = array_reduce($this->route_registry, function ($matched_route_registrant, $route_registrant) use ($request) {
      if (!isset($matched_route_registrant) && self::requestMatchesRegistrant($route_registrant, $request)) {
        $matched_route_registrant = $route_registrant + [
          'parsed_params' => self::extractPathArguments($route_registrant, $request),
        ];
      }

      return $matched_route_registrant;
    }, NULL);

    if (!isset($matched_registrant)) {
      throw new MissingRouteException();
    }

    return call_user_func_array($matched_registrant['callback'], array_merge(array_values($matched_registrant['parsed_params']), [
      $request,
    ]));

  }

  public function run(Request $request) {

    try {
      $response = $this->getResponse($request);

      if (is_string($response)) {
        header('Content-Type: text/html');
        print $response;
      }
      elseif ($response instanceOf \SimpleXMLElement) {
        header('Content-Type: application/xml');
        print $response->saveXML();
      }
      else {
        header('Content-Type: application/json');
        print json_encode($response, JSON_PRETTY_PRINT);
      }

    }
    catch (\Exception $e) {
      $original_exception_class = get_class($e);
      if (isset($this->config['exception_map'][$original_exception_class])) {
        /** @var \Exception $mapped_exception_class */
        $mapped_exception_class = $this->config['exception_map'][$original_exception_class];
        $mapped_exception = new $mapped_exception_class();
      }
      else {
        $mapped_exception = new Http500Exception();
      }
      $exception_header_message = sprintf('%s %s %s', $request->protocol, $mapped_exception->getCode(), $mapped_exception->getMessage());
      header('Content-Type: text/html');
      header($exception_header_message, TRUE, $mapped_exception->getCode());
      print $exception_header_message;
    }

  }

}
