<?php

namespace App;

use Blanket\Model;

/**
 * Class Muppet
 *
 * @property int id
 * @property string name
 * @property string occupation
 *
 * @package App
 */
class Muppet extends Model {

  protected static $table = 'muppets';

}