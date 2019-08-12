<?php
/**
 * Created by PhpStorm.
 * User: F
 * Date: 2019/7/17
 * Time: 2:42
 */

namespace core\spider;

use core\spider\FSpider;

class Action
{
    public static function start($argv)
    {
        if (!isset($argv[1])) exit('请输入抓取地址');

        if (!isset($argv[2])) exit('请输入抓取深度');

        new FSpider($argv);
    }

}