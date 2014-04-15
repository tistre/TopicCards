<?php

namespace Xddb\Backends\Db;


class Services implements \Xddb\Interfaces\iServices
{
    public $topicmap;
    public $db_utils;
    public $db = false;
    
    
    public function __construct()
    {
        $this->db_utils = new DbUtils($this);
    }
    
    
    public function log($level, $msg)
    {
        error_log("[$level] $msg");
    }
    
    
    public function getDbParams()
    {
        return array
        (
            'dsn' => 'mysql:host=localhost;dbname=xddb_test;charset=utf8mb4', 
            'username' => 'tstrehle', 
            'password' => 'secret',
            'driver_options' => array( \PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='STRICT_ALL_TABLES'" )
        );
    }    
}
