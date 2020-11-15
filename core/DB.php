<?php

namespace core;

use core\pattern\Singleton;
use PDO;
use core\Config;

class DB extends Singleton {

    private $db;


    public static function getDB(){
        $db = static::getInstance();
        return $db->connection();
    }

    protected function connection(){

        $mysql_config = Config::get("db.mysql");

        $server     = $mysql_config["host"];
        $user       = $mysql_config["user"];
        $pass       = $mysql_config["password"];
        $port       = $mysql_config["port"];
        $database   = $mysql_config["database"];

        try {
            if (is_null($this->db)) {
                $this->db = new PDO("mysql:host=$server;dbname=$database;port=$port", $user, $pass);
            }

        } catch (\PDOException $e) {
            \error_log("Connection failed: " . $e->getMessage());
        }

        return $this->db;

    }
}