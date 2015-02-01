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
            'name' => [ ],
            'has_name_type' => [ ],
            'topic_type' => $this->getTypes([ ]),
            'subject' => array_merge($this->getSubjectIdentifiers(), $this->getSubjectLocators()),
            'occurrence' => [ ],
            'has_occurrence_type' => [ ]
        ];
        
        foreach ($this->getNames([ ]) as $name)
        {
            $result[ 'name' ][ ] = $name->getValue();
            $result[ 'has_name_type' ][ ] = $name->getType();
        }

        foreach ($this->getOccurrences([ ]) as $occurrence)
        {
            $result[ 'occurrence' ][ ] = $occurrence->getValue();
            $result[ 'has_occurrence_type' ][ ] = $occurrence->getType();
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
