<?php

use Blanket\App;
use Blanket\Request;
use Muppet\Muppet;

require 'vendor/autoload.php';

define('WEBROOT', __DIR__);

$app = new App([
  'exception_map' => [
    \Muppet\RecordNotFoundException::class => \Blanket\Http404Exception::class,
    \Blanket\MissingRouteException::class => \Blanket\Http404Exception::class,
  ],
]);

$app->post('muppets', function (Request $request) {
  return Muppet::create($request->post_data)->getAttributes();
});

$app->get('muppets/:id', function ($id, Request $request) {
  return Muppet::findOrFail($id)->getAttributes();
});

$app->put('muppets/:id', function ($id, Request $request) {
  return Muppet::findOrFail($id)
    ->update($request->put_data)
    ->saveIfChanged()
    ->getAttributes();
});

$app->del('muppets/:id', function ($id, Request $request) {
  return Muppet::findOrFail($id)
    ->delete()
    ->getAttributes();
});

$app->get('muppets', function (Request $request) {
  $page = 1;
  $per_page = 10;
  $instances = Muppet::all($page, $per_page);
  $total = count($instances);
  $muppets = array_map(function (Muppet $muppet) {
    return $muppet->getAttributes();
  }, $instances);
  return compact('total', 'page', 'per_page', 'muppets');
});

$app->run(Request::createFromGlobals());
