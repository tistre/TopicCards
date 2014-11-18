<?php

namespace TopicBank\Backends\Db;


// XXX reuse for associations

trait TopicSearchAdapter
{
    protected function index()
    {
        $ok = $this->services->search_utils->init();
        
        if ($ok < 0)
            return $ok;

        $response = $this->services->search->index(array
        (
            'index' => $this->topicmap->getSearchIndex(),
            'type' => 'topic',
            'id' => $this->getId(),
            'body' => $this->getIndexFields()
        ));
        
        if (! $response[ 'created' ])
            return -1;
        
        return 1;
    }
    
    
    protected function getIndexFields()
    {
        return
        [
            'name' => $this->getLabel()
        ];
    }
    
    
    public function getIndexData()
    {
        $ok = $this->services->search_utils->init();
        
        if ($ok < 0)
            return $ok;

        return $this->services->search->get(array
        (
            'index' => $this->topicmap->getSearchIndex(),
            'type' => 'topic',
            'id' => $this->getId()
        ));
    }    
}
