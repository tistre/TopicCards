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

        $callback_result = [ ];

        $this->topicmap->trigger
        (
            \TopicBank\Interfaces\iTopic::EVENT_INDEXING, 
            [ 'topic' => $this, 'index_fields' => $result ],
            $callback_result
        );
        
        if (isset($callback_result[ 'index_fields' ]) && is_array($callback_result[ 'index_fields' ]))
            $result = $callback_result[ 'index_fields' ];
        
        return $result;
    }
}
