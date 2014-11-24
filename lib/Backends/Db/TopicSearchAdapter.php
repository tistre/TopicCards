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
    
    
    public function removeFromIndex()
    {
        $ok = $this->services->search_utils->init();
        
        if ($ok < 0)
            return $ok;

        try
        {
            $response = $this->services->search->delete(array
            (
                'index' => $this->topicmap->getSearchIndex(),
                'type' => 'topic',
                'id' => $this->getId()
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
            'label' => $this->getLabel(),
            'type_id' => [ ] 
        ];
        
        foreach ($this->getTypes([ ]) as $type_id)
            $result[ 'type_id' ][ ] = $type_id;
        
        foreach ($this->getNames([ ]) as $name)
        {
            $search_fields = $this->getSearchFieldsByType($name->getType());
            
            foreach ($search_fields as $search_field)
            {
                if (! isset($result[ $search_field ]))
                    $result[ $search_field ] = [ ];
                    
                $result[ $search_field ][ ] = $name->getValue();
            }
        }

        // XXX add occurrences here
        
        return $result;
    }
    
    
    protected function getSearchFieldsByType($type_id)
    {
        static $search_field_type = false;
        
        if ($search_field_type === false)
            $search_field_type = $this->topicmap->getTopicBySubjectIdentifier('http://www.strehle.de/schema/searchField');
        
        $type_topic = $this->topicmap->newTopic();
        $type_topic->load($type_id);
        
        $result = [ ];
        
        foreach ($type_topic->getNames([ ]) as $name)
        {
            if ($name->getType() !== $search_field_type)
                continue;
                
            $result[ ] = $name->getValue();
        }
        
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
