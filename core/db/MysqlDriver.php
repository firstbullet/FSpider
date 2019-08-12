<?php
/**
 * Created by PhpStorm.
 * User: F
 * Date: 2019/7/17
 * Time: 1:23
 */

namespace core\db;

class MysqlDriver
{
    private $hostname = CONFIG['hostname'];
    private $databases = CONFIG['databases'];
    private $username = CONFIG['username'];
    private $password = CONFIG['password'];
    private $db;

    public function __construct()
    {
        $this->mysqlConnect();
    }

    private function mysqlConnect()
    {
        //连接数据库
        $this->db = new \mysqli($this->hostname, $this->username, $this->password, $this->databases);

        if ($this->db->connect_errno) {

            exit('数据库连接失败');

        }

        return $this;
    }

    public function select()
    {

        $this->db->query("set names utf8");


        $result = $this->db->query('select * from fs_content');


        $num_results = $result->num_rows;

        $row = [];

        for ($i = 0; $i < $num_results; $i++) {

            $row[] = $result->fetch_assoc();
        }


        $result->free();

        $this->db->close();

        return $row;

    }

    public function insert($insert)
    {
        $this->db->query("set names utf8");

        $result = $this->db->query($insert);

        if (!$result) {

            echo "Mysql error ：" . $this->db->error;

        }
        return $this->db->insert_id;
    }

}