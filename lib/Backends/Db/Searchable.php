<?php

namespace TopicBank\Backends\Db;


trait Searchable
{
    public function index()
    {
        $response = $this->services->search->index($this->topicmap, 
        [
            'type' => '_doc',
            'id' => $this->getId(),
            'body' => $this->getIndexFields()
        ]);

        if ($response === false)
            return -1;
        
        return 1;
    }
    
    
    public function removeFromIndex()
    {
        $response = $this->services->search->delete($this->topicmap,
        [
            'type' => '_doc',
            'id' => $this->getId()
        ]);

        if ($response === false)
            return -1;
        
        return 1;
    }

    
    public function getIndexedData()
    {
        return $this->services->search->get($this->topicmap,
        [
            'type' => '_doc',
            'id' => $this->getId()
        ]);
    }    
}
