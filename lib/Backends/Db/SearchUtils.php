<?php

namespace TopicBank\Backends\Db;


class SearchUtils
{
    protected $services;
    
    
    public function __construct(\TopicBank\Interfaces\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function init()
    {
        if ($this->services->search !== false)
            return 0;
        
        $search_params = $this->services->getSearchParams();
        
        $this->services->search = new \Elasticsearch\Client();
        
        return 1;
    }
}
