<?php
require 'vendor/autoload.php';

use App\Muppet;

use Blanket\App;
use Blanket\Db\Db;
use Blanket\Request;

$config = [
  'resources' => [
    'muppets' => Muppet::class,
  ],
  'storage' => (new Db(sprintf('sqlite:%s/storage/db.sqlite', __DIR__))),
  'allow_origin' => 'http://localhost:8000',
];

$app = new App($config);

$app->run(Request::createFromGlobals());
