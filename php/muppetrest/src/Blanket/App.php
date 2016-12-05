<?php

namespace Blanket;
use Muppet\RecordNotFoundException;

/**
 * Class App
 *
 * @method get($path, \Closure $closure)
 * @method post($path, \Closure $closure)
 * @method put($path, \Closure $closure)
 * @method delete
 *
 * @package Blanket
 */
class App {

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
      throw new \LogicException();
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
      header($request->protocol . ' 500 Internal Server Error', TRUE, 500);
    }

  }


}
