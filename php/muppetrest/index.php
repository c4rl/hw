<?php

use Blanket\App;
use Blanket\Request;
use Muppet\RecordNotFoundException;

require 'vendor/autoload.php';

define('WEBROOT', __DIR__);

$app = new App();

$app->get('muppets', function (Request $request) {
  return Muppet\Muppet::all();
});

$app->get('muppets/:id', function ($id, Request $request) {
  try {
    return Muppet\Muppet::findOrFail($id)->getAttributes();
  }
  catch (RecordNotFoundException $e) {
    return 'Not found';
  }
});

$app->post('muppets', function (Request $request) {
  return Muppet\Muppet::create($request->params)->getAttributes();
});

$app->run(Request::createFromGlobals());
