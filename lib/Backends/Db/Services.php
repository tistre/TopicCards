<?php

namespace Xddb\Backends\Db;


class Services implements \Xddb\Interfaces\iServices
{
    public $topicmap;
    public $db_utils;
    public $db = false;
    
    protected $db_params = [ ];
    
    
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
        return $this->db_params;
    }  
    
    
    public function setDbParams(array $params)
    {
        /*
        
        Example:
        
        $services->setDbParams(
        [
            'dsn' => 'mysql:host=localhost;dbname=xddb_test;charset=utf8mb4', 
            'username' => 'user', 
            'password' => 'secret',
            'driver_options' => [ \PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='STRICT_ALL_TABLES'" ]
        ]);
        
        */

        $this->db_params = $params;
        return 1;
    }
}
