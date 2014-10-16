<?php

namespace TopicBank\Backends\Db;


class Services implements \TopicBank\Interfaces\iServices
{
    public $db_utils;
    public $db = false;
    
    protected $tm_system;
    
    protected $db_params = [ ];
    
    
    public function __construct()
    {
        $this->db_utils = new DbUtils($this);
        $this->tm_system = new TopicMapSystem($this);
    }
    

    public function getTopicMapSystem()
    {
        return $this->tm_system;
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
        // See include/config-sample.php for the $params format
                
        $this->db_params = $params;
        return 1;
    }
}
