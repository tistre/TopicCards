<?php

namespace TopicBank\Backends\Db;


// XXX reuse for associations

trait TopicSearchAdapter
{
    public function index()
    {
        $response = $this->services->search->index($this->topicmap, 
        [
            'type' => 'topic',
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
            'type' => 'topic',
            'id' => $this->getId()
        ]);

        if ($response === false)
            return -1;
        
        return 1;
    }

    
    protected function getIndexFields()
    {
        $result = 
        [ 
            'label' => $this->getLabel(),
            'type_id' => [ ] 
        ];
        
        foreach ($this->getTypeIds([ ]) as $type_id)
            $result[ 'type_id' ][ ] = $type_id;
        
        foreach ($this->getNames([ ]) as $name)
        {
            $search_fields = $this->getSearchFieldsByType($name->getTypeId());
            
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
            $search_field_type = $this->topicmap->getTopicIdBySubject('http://www.strehle.de/schema/searchField');
        
        $type_topic = $this->topicmap->newTopic();
        $type_topic->load($type_id);
        
        $result = [ ];
        
        foreach ($type_topic->getNames([ ]) as $name)
        {
            if ($name->getTypeId() !== $search_field_type)
                continue;
                
            $result[ ] = $name->getValue();
        }
        
        return $result;
    }
    
    
    public function getIndexedData()
    {
        return $this->services->search->get($this->topicmap,
        [
            'type' => 'topic',
            'id' => $this->getId()
        ]);
    }    
}
