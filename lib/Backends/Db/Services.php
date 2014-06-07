<?php

namespace TopicBank\Backends\Db;


class Services implements \TopicBank\Interfaces\iServices
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
        // See include/config-sample.php for the $params format
                
        $this->db_params = $params;
        return 1;
    }
}
