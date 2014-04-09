<?php

namespace Xddb\Backends\Db;


class TopicMapSystem extends Core implements \Xddb\Interfaces\iTopicMapSystem
{
    protected $topicmaps = array();
    
    
    public function newTopicMap()
    {
        $topicmap = new TopicMap($this->services);
        
        $this->topicmaps[ ] = $topicmap;
        
        return $topicmap;
    }
    
    
    public function getTopicMaps()
    {
        $result = array();
        
        foreach ($this->topicmaps as $topicmap)
            $result[ ] = $topicmap->getUrl();
            
        return $result;
    }
}
