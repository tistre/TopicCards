<?php

namespace TopicBank\Backends\Db;


class Core implements \TopicBank\Interfaces\iCore
{
    protected $services;
    
    
    public function __construct(\TopicBank\Interfaces\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function getServices()
    {
        return $this->services;
    }
}
