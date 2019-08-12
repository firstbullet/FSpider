<?php
/**
 * Created by PhpStorm.
 * User: F
 * Date: 2019/7/16
 * Time: 19:31
 */

define('BASEDIR', str_replace('\\', '/', __DIR__));

require_once BASEDIR . '/core/Loader.php';

require_once BASEDIR . '/core/config.php';

define('CONFIG',$_CFG);

spl_autoload_register('\\core\\Loader::autoload');

core\spider\Action::start($argv);