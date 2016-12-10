<?php

use Blanket\App;
use Blanket\Request;

require 'vendor/autoload.php';

$app = new App();

$app->run(Request::createFromGlobals());
