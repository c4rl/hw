<?php

use Blanket\App;
use Blanket\Request;
use Muppet\Muppet;
use Muppet\RecordNotFoundException;

require 'vendor/autoload.php';

define('WEBROOT', __DIR__);

$app = new App();

$app->post('muppets', function (Request $request) {
  return Muppet::create($request->post_data)->getAttributes();
});

$app->get('muppets/:id', function ($id, Request $request) {
  try {
    return Muppet::findOrFail($id)->getAttributes();
  }
  catch (RecordNotFoundException $e) {
    return 'Not found';
  }
});

$app->put('muppets/:id', function ($id, Request $request) {
  try {
    return Muppet::findOrFail($id)
      ->update($request->put_data)
      ->saveIfChanged()
      ->getAttributes();
  }
  catch (RecordNotFoundException $e) {
    return 'Not found';
  }
});

$app->del('muppets/:id', function ($id, Request $request) {
  try {
    return Muppet::findOrFail($id)
      ->delete()
      ->getAttributes();
  }
  catch (RecordNotFoundException $e) {
    return 'Not found';
  }
});

$app->get('muppets', function (Request $request) {
  return Muppet::all();
});

$app->run(Request::createFromGlobals());
