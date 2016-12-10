<?php

use Blanket\App;
use Blanket\Db\Db;
use Blanket\Request;
use Muppet\Muppet;

require 'vendor/autoload.php';

define('WEBROOT', __DIR__);

$app = new App([
  'models' => [
    Muppet::class,
  ],
  'storage' => (new Db(sprintf('sqlite:%s/storage/db.sqlite', WEBROOT))),
]);

$app->post('muppets', function (Request $request) {
  return Muppet::create($request->post_data)->getAttributes();
});

$app->get('muppets/:id', function ($id, Request $request) {
  return Muppet::findOrFail($id)->getAttributes();
});

$app->put('muppets/:id', function ($id, Request $request) {
  return Muppet::findOrFail($id)
    ->updateAttributes($request->put_data)
    ->saveIfChanged()
    ->getAttributes();
});

$app->delete('muppets/:id', function ($id, Request $request) {
  return Muppet::findOrFail($id)
    ->delete()
    ->getAttributes();
});

$app->get('muppets', function (Request $request) {
  $page = isset($request->get_data['page']) ? (int) $request->get_data['page'] : 1;
  $per_page = isset($request->get_data['per_page']) ? (int) $request->get_data['per_page'] : 10;
  $instances = Muppet::all($page, $per_page);
  $total = count($instances);
  $muppets = array_map(function (Muppet $muppet) {
    return $muppet->getAttributes();
  }, $instances);
  return compact('total', 'page', 'per_page', 'muppets');
});

$app->run(Request::createFromGlobals());
