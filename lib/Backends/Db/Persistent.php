<?php

namespace Xddb\Backends\Db;


trait Persistent
{
    protected $topicmap;
    protected $id;
    
    
    public function getTopicMap()
    {
        return $this->topicmap;
    }
    
    
    public function setTopicMap(\Xddb\Interfaces\iTopicMap $topicmap)
    {
        $this->topicmap = $topicmap;
        return 1;
    }

    
    public function getId()
    {
        return $this->id;
    }
    
    
    public function setId($id)
    {
        $this->id = $id;
        return 1;
    }
    
    
    public function load($id)
    {
        return -1;
    }
    
    
    public function save()
    {
        return -1;
    }
    
    
    public function delete()
    {
        return -1;
    }
}
