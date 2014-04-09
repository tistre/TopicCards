<?php

namespace Xddb\Backends\Db;


class Core implements \Xddb\Interfaces\iCore
{
    protected $services;
    
    
    public function __construct(\Xddb\Interfaces\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function getServices()
    {
        return $this->services;
    }
}
