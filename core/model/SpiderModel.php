<?php
/**
 * Created by PhpStorm.
 * User: F
 * Date: 2019/7/18
 * Time: 18:05
 */

namespace core\model;

use core\db\MysqlDriver;
use core\db\PdoDriver;

class SpiderModel
{
    protected $pdo;

    protected $mysql;

    private $port = CONFIG['port'];

    private $prefix = CONFIG['prefix'];

    private $charset = CONFIG['charset'];


    public function __construct()
    {
        $this->mysql = new MysqlDriver;
    }

    public function insert($arr)
    {
        $sql = "INSERT INTO " . $this->prefix . "content (`title`, `content`, `url`, `img`, `add_time`, `from`) 
                VALUES('{$arr['title']}','{$arr['content']}','{$arr['url']}','{$arr['img']}','{$arr['add_time']}','{$arr['from']}')";

        $data = $this->mysql->insert($sql);

        return $data;
    }

    public function select()
    {
        return $this->mysql->select();
    }

}