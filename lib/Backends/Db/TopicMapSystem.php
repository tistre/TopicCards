<?php

namespace TopicBank\Backends\Db;


class TopicMapSystem implements \TopicBank\Interfaces\iTopicMapSystem
{
    protected $topicmaps = array();
    protected $services;
    
    
    public function __construct(\TopicBank\Interfaces\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function getServices()
    {
        return $this->services;
    }
    

    public function newTopicMap($key)
    {
        $topicmap = new TopicMap($this->services);
        
        $this->topicmaps[ $key ] = $topicmap;
        
        return $topicmap;
    }
    

    public function getTopicMap($key)
    {
        if (! $this->hasTopicMap($key))
            return false;
            
        return $this->topicmaps[ $key ];
    }
    
    
    public function hasTopicMap($key)
    {
        return isset($this->topicmaps[ $key ]);
    }
    
    
    public function getTopicMapKeys()
    {
        return array_keys($this->topicmaps);
    }
}
