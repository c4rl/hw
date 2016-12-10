<?php

use Blanket\App;
use Blanket\Db\Db;
use Blanket\Request;

use Muppet\Muppet;

require 'vendor/autoload.php';

$config = [
  'resources' => [
    'muppets' => Muppet::class,
  ],
  'storage' => (new Db(sprintf('sqlite:%s/storage/db.sqlite', __DIR__))),
];

(new App($config))->run(Request::createFromGlobals());
