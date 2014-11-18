<?php

namespace TopicBank\Backends\Db;


// XXX reuse for associations

trait TopicSearchAdapter
{
    public function index()
    {
        $ok = $this->services->search_utils->init();
        
        if ($ok < 0)
            return $ok;

        try
        {
            $response = $this->services->search->index(array
            (
                'index' => $this->topicmap->getSearchIndex(),
                'type' => 'topic',
                'id' => $this->getId(),
                'body' => $this->getIndexFields()
            ));
        }
        catch (\Exception $e)
        {
            trigger_error(sprintf("%s: %s", __METHOD__, $e->getMessage()), E_USER_WARNING);
            return -1;
        }
        
        return 1;
    }
    
    
    protected function getIndexFields()
    {
        $result = 
        [ 
            'name' => [ ],
            'type' => [ ]
        ];
        
        foreach ($this->getNames([ ]) as $name)
            $result[ 'name' ][ ] = $name->getValue();

        foreach ($this->getTypes([ ]) as $type_id)
            $result[ 'type' ][ ] = $type_id;
        
        return $result;
    }
    
    
    public function getIndexedData()
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
