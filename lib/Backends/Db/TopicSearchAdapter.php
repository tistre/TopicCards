<?php

namespace TopicBank\Backends\Db;


trait TopicSearchAdapter
{
    protected function getSearchType()
    {
        return 'topic';
    }
    
    
    protected function getIndexFields()
    {
        $result = 
        [ 
            // XXX add sort date
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
}
