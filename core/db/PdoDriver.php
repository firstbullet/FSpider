<?php
/**
 * Created by PhpStorm.
 * User: F
 * Date: 2019/7/17
 * Time: 1:23
 */

namespace core\db;

class PdoDriver
{
    private $dsn;

    private $username = CONFIG['username'];

    private $password = CONFIG['password'];

    private $prefix = CONFIG['prefix'];

    private $charset = CONFIG['charset'];

    private $db;


    public function __construct()
    {

        $this->dsn = "mysqli:host=" . CONFIG['hostname'] . ";dbname=" . CONFIG['databases'];
        $this->pdoConnect();

    }

    private function pdoConnect()
    {
        try {

            $this->db = new \PDO($this->dsn, $this->username, $this->password);

        } catch (\PDOException $e) {

            echo 'PDO连接数据库失败: ' . $e->getMessage();

        }
    }

    public function insert($arr)
    {
        $sql = "INSERT INTO " . $this->prefix . "content (`title`, `content`, `url`, `img`, `add_time`, `from`) VALUES('{$arr['title']}', '{$arr['content']}', '{$arr['url']}', '{$arr['img']}', '{$arr['add_time']}', '{$arr['from']}')";

        $result = $this->db->query($sql);

        $result = $result->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function select()
    {
        $sql = 'SELECT * FROM ' . $this->prefix . 'content';

        $this->db->query("set names $this->charset");

        $result = $this->db->query($sql);

        $data = $result->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }
}