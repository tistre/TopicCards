<?php

namespace TopicCards\DbBackend;


trait Searchable
{
    public function index()
    {
        $response = $this->services->search->index($this->topicmap, 
        [
            'type' => $this->getSearchType(),
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
            'type' => $this->getSearchType(),
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
            'type' => $this->getSearchType(),
            'id' => $this->getId()
        ]);
    }    
}
