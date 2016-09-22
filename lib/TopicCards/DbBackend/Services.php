<?php

namespace TopicCards\DbBackend;


class Services implements \TopicCards\iServices
{
    // XXX replace with private + accessor methods?
    public $db_utils;
    public $db = false;
    public $search;
    
    protected $tm_system;
    
    protected $db_params = [ ];
    protected $search_params = [ ];
    
    // Preferred scopes for name display = labels. Used to set preferred display language.
    protected $preferred_label_scopes = [ [ ], '*' ];
    
    
    public function __construct()
    {
        $this->db_utils = new DbUtils($this);
        $this->search = new Search($this);
        $this->tm_system = new TopicMapSystem($this);
    }
    

    public function getTopicMapSystem()
    {
        return $this->tm_system;
    }


    public function setLogger(\Monolog\Logger $logger)
    {
        $this->logger = $logger;
    }


    public function getLogger()
    {
        return $this->logger;
    }


    // TODO implement / switch to logger interface / library
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
    
    
    public function getSearchParams()
    {
        return $this->search_params;
    }  
    
    
    public function setSearchParams(array $params)
    {
        // See include/config-sample.php for the $params format
                
        $this->search_params = $params;
        return 1;
    }
    
    
    public function getPreferredLabelScopes()
    {
        return $this->preferred_label_scopes;
    }
    
    
    public function setPreferredLabelScopes(array $scopes)
    {
        $this->preferred_label_scopes = $scopes;
        return 1;
    }
}
