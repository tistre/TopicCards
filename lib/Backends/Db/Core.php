<?php

namespace TopicBank\Backends\Db;


class Core implements \TopicBank\Interfaces\iCore
{
    protected $services;
    protected $topicmap;
    
    
    public function __construct(\TopicBank\Interfaces\iServices $services, \TopicBank\Interfaces\iTopicMap $topicmap)
    {
        $this->services = $services;
        $this->topicmap = $topicmap;
    }
    
    
    public function getServices()
    {
        return $this->services;
    }


    public function getTopicMap()
    {
        return $this->topicmap;
    }
}
