<?php

namespace Blanket;

class Http500Exception extends \RuntimeException {

  protected $message = 'Internal Server Error';

  protected $code = 500;

}