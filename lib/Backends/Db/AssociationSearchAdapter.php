<?php

namespace TopicBank\Backends\Db;


trait AssociationSearchAdapter
{
    protected function getSearchType()
    {
        return 'association';
    }
        
    
    protected function getIndexFields()
    {
        $result = 
        [ 
            // XXX add sort date
            'association_type' => $this->getType(),
            'has_role_type' => [ ],
            'has_player_id' => [ ]
        ];
        
        foreach ($this->getRoles([ ]) as $role)
        {
            $result[ 'has_role_type' ][ ] = $role->getType();
            $result[ 'has_player_id' ][ ] = $role->getPlayerId();
        }

        $callback_result = [ ];

        $this->topicmap->trigger
        (
            \TopicBank\Interfaces\iAssociation::EVENT_INDEXING, 
            [ 'association' => $this, 'index_fields' => $result ],
            $callback_result
        );
        
        if (isset($callback_result[ 'index_fields' ]) && is_array($callback_result[ 'index_fields' ]))
            $result = $callback_result[ 'index_fields' ];
                
        return $result;
    }
}