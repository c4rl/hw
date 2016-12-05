<?php

namespace Blanket;

class Http404Exception extends \RuntimeException {

  protected $message = 'Not Found';

  protected $code = 404;

}