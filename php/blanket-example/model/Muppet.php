<?php

namespace Muppet;

use Blanket\Model;

/**
 * Everybody loves muppets.
 *
 * @property int $id Primary key.
 * @property string $name What is ur name?
 * @property string $occupation What is ur occupation?
 */
class Muppet extends Model {

  /**
   * {@inheritdoc}
   */
  protected static $table = 'muppets';

}
