<?php
/**
 * Created by PhpStorm.
 * User: F
 * Date: 2019/7/17
 * Time: 1:40
 */

namespace core;

class Loader
{
    static function autoload($class)
    {
        require_once BASEDIR . '/' . str_replace('\\', '/', $class) . '.php';
    }
}
