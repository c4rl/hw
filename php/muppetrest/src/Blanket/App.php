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

  public function __construct(array $config = []) {
    $this->config = $config;
  }

  private $config = [];

  private $registry = [];

  public function __call($method, array $arguments) {

    if (count($arguments) != 2) {
      throw new \ArgumentCountError();
    }

    list($path, $callback) = $arguments;

    if (!($callback instanceof \Closure)) {
      throw new \TypeError();
    }

    $this->registry[] = compact('method', 'path', 'callback');
  }

  public static function pathMaskToRegex($path_mask) {
    $with_regex_placeholders = preg_replace('/:[a-z]+/', '(.+?)', $path_mask);
    $escaped = preg_replace('/\//', '\/', $with_regex_placeholders);
    return sprintf('/^%s$/', $escaped);
  }

  public static function pathMaskToNames($path_mask) {
    $pieces = explode('/', $path_mask);
    return array_reduce($pieces, function (array $names, $piece) {
      return $piece[0] == ':' ? array_merge($names, [
        str_replace(':', ':', $piece),
      ]) : $names;
    }, []);
  }

  public function getResponse(Request $request) {

    $matched_registrant = array_reduce($this->registry, function ($matched_registrant, $registrant) use ($request) {
      if (isset($matched_registrant)) {
        return $matched_registrant;
      }

      $regex = self::pathMaskToRegex($registrant['path']);
      $mapped_names = self::pathMaskToNames($registrant['path']);
      $matches = [];
      if (preg_match($regex, $request->path, $matches) && $request->method == $registrant['method']) {
        array_shift($matches);
        $registrant['parsed_params'] = array_combine($mapped_names, $matches);
        return $registrant;
      }

      return NULL;

    }, NULL);

    if (!isset($matched_registrant)) {
      throw new MissingRouteException();
    }

    return call_user_func_array($matched_registrant['callback'], array_merge($matched_registrant['parsed_params'], [
      $request,
    ]));

  }

  public function run(Request $request) {

    try {
      $response = $this->getResponse($request);

      if (is_string($response)) {
        header('Content-Type: application/json');
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
