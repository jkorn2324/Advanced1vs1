<?php
/**
 * Created by PhpStorm.
 * User: jkorn2324
 * Date: 2020-06-05
 * Time: 17:56
 */

declare(strict_types=1);

namespace jkorn\ad1vs1\level\generators\types\low_ceil;


use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\level\generators\types\AD1vs1DefaultRed;

class DefaultRedLowCeil extends AD1vs1DefaultRed
{

    protected $height = AD1vs1Util::CEILING_LOW;

}