<?php

namespace TopicCards\DbBackend;


use TopicCards\iTopicMap;

trait TopicMapDbAdapter
{
    public function selectTopics(array $filters)
    {
        if (! isset($filters['limit']))
        {
            $filters['limit'] = 500;
        }

        if (isset($filters['type']))
        {
            $filters['type_id'] = $this->getTopicIdBySubject($filters['type']);
        }

        $ok = $this->services->db_utils->connect();

        if ($ok < 0)
        {
            return $ok;
        }

        $classes = [ 'Topic' ];

        if (! empty($filters['type_id']))
        {
            $classes[] = $filters['type_id'];
        }

        $query = sprintf
        (
            'MATCH (t%s)',
            $this->services->db_utils->labelsString($classes)
        );

        $bind = [ ];

        if (! empty($filters['name_like']))
        {
            $query .= '-[:hasName]->(n:Name) WHERE lower(n.value) CONTAINS lower({name_like})';
            $bind[ 'name_like' ] = $filters['name_like']; 
        }

        $query .= ' RETURN DISTINCT t.id';

        if ($filters[ 'limit' ] > 0)
        {
            $query .= ' LIMIT ' . $filters[ 'limit' ];
        }

        $this->logger->addInfo($query, $bind);

        try
        {
            $qresult = $this->services->db->run($query, $bind);
        }
        catch (\GraphAware\Neo4j\Client\Exception\Neo4jException $exception)
        {
            $this->logger->addError($exception->getMessage());
            // TODO: Error handling
            return -1;
        }

        $result = [ ];

        foreach ($qresult->getRecords() as $record)
        {
            $result[ ] = $record->get('t.id');
        }

        return $result;
    }
    
    
    public function selectTopicBySubject($uri)
    {
        if (strlen($uri) === 0)
        {
            return false;
        }
        
        $ok = $this->services->db_utils->connect();

        if ($ok < 0)
        {
            return $ok;
        }

        $query = 'MATCH (n:Topic) WHERE {uri} in n.subject_identifiers RETURN n.id';
        $bind = [ 'uri' => $uri ];

        $this->logger->addInfo($query, $bind);

        try
        {
            $qresult = $this->services->db->run($query, $bind);
        }
        catch (\GraphAware\Neo4j\Client\Exception\Neo4jException $exception)
        {
            $this->logger->addError($exception->getMessage());
            // TODO: Error handling
            return -1;
        }

        foreach ($qresult->getRecords() as $record)
        {
            return $record->get('n.id');
        }

        return false;
    }
    
    
    public function selectTopicSubjectIdentifier($topic_id)
    {
        return $this->selectTopicSubject($topic_id, 'subject_identifiers');
    }
    
    
    public function selectTopicSubjectLocator($topic_id)
    {
        return $this->selectTopicSubject($topic_id, 'subject_locators');
    }
    
    
    protected function selectTopicSubject($topic_id, $what)
    {
        if (strlen($topic_id) === 0)
        {
            return false;
        }
        
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
        {
            return false;
        }

        $query = 'MATCH (topic { id: {id} }) RETURN topic.' . $what;
        $bind = [ 'id' => $topic_id ];

        $this->logger->addInfo($query, $bind);
        
        try
        {
            $qresult = $this->services->db->run($query, $bind);
        }
        catch (\GraphAware\Neo4j\Client\Exception\Neo4jException $exception)
        {
            $this->logger->addError($exception->getMessage());
            return false;
        }

        // TODO add error handling

        $record = $qresult->getRecord();
        
        if (empty($record))
        {
            return false;
        }
        
        $values = $record->get('topic.' . $what);
        
        if (empty($values))
        {
            return false;
        }
        
        return $values[ 0 ];
    }
    
    
    public function selectAssociations(array $filters)
    {
        if (isset($filters[ 'type' ]))
        {
            $filters[ 'type_id' ] = $this->getTopicIdBySubject($filters[ 'type' ]);
        }

        if (isset($filters[ 'role_player' ]))
        {
            $filters[ 'role_player_id' ] = $this->getTopicIdBySubject($filters[ 'role_player' ]);
        }
        
        if (isset($filters[ 'role_type' ]))
        {
            $filters[ 'role_type_id' ] = $this->getTopicIdBySubject($filters[ 'role_type' ]);
        }
        
        if (! isset($filters[ 'limit' ]))
        {
            $filters[ 'limit' ] = 500;
        }
            
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;

        $classes = [ 'Association' ];

        if (! empty($filters['type_id']))
        {
            $classes[] = $filters['type_id'];
        }

        $query = sprintf
        (
            'MATCH (a%s)',
            $this->services->db_utils->labelsString($classes)
        );

        $bind = [ ];

        if ((! empty($filters['role_player_id'])) && (! empty($filters['role_type_id']))) 
        {
            $query .= sprintf
            (
                '-[%s]-(t:Topic { id: {player_id} })',
                $this->services->db_utils->labelsString([ $filters['role_type_id'] ])
            );
            
            $bind[ 'player_id' ] = $filters['role_player_id'];
        }
        elseif (! empty($filters['role_player_id']))
        {
            $query .= '--(t:Topic { id: {player_id} })';
            $bind[ 'player_id' ] = $filters['role_player_id'];
        }
        elseif (! empty($filters['role_type_id']))
        {
            $query .= sprintf
            (
                '-[%s]-(t:Topic)',
                $this->services->db_utils->labelsString([ $filters['role_type_id'] ])
            );
        }

        $query .= ' RETURN DISTINCT a.id';

        if ($filters[ 'limit' ] > 0)
        {
            $query .= ' LIMIT ' . $filters[ 'limit' ];
        }

        $this->logger->addInfo($query, $bind);

        try
        {
            $qresult = $this->services->db->run($query, $bind);
        }
        catch (\GraphAware\Neo4j\Client\Exception\Neo4jException $exception)
        {
            $this->logger->addError($exception->getMessage());
            // TODO: Error handling
            return -1;
        }

        $result = [ ];

        foreach ($qresult->getRecords() as $record)
        {
            $result[ ] = $record->get('a.id');
        }

        return $result;
    }
    
    
    public function selectTopicTypes(array $filters)
    {
        return $this->selectWhat(iTopicMap::SUBJECT_TOPIC_TYPE, $filters);
    }
    
    
    public function selectNameTypes(array $filters)
    {
        return $this->selectWhat(iTopicMap::SUBJECT_TOPIC_NAME_TYPE, $filters);
    }
    

    public function selectNameScopes(array $filters)
    {
        // XXX selects all scopes, not just name scopes
        return $this->selectWhat(iTopicMap::SUBJECT_SCOPE, $filters);
    }
    
    
    public function selectOccurrenceTypes(array $filters)
    {
        return $this->selectWhat(iTopicMap::SUBJECT_OCCURRENCE_TYPE, $filters);
    }


    public function selectOccurrenceDatatypes(array $filters)
    {
        return $this->selectWhat(iTopicMap::SUBJECT_DATATYPE, $filters);
    }

    
    public function selectOccurrenceScopes(array $filters)
    {
        // XXX selects all scopes, not just occurrence scopes
        return $this->selectWhat(iTopicMap::SUBJECT_SCOPE, $filters);
    }
    
    
    public function selectAssociationTypes(array $filters)
    {
        return $this->selectWhat(iTopicMap::SUBJECT_ASSOCIATION_TYPE, $filters);
    }
    
    
    public function selectAssociationScopes(array $filters)
    {
        // XXX selects all scopes, not just association scopes
        return $this->selectWhat(iTopicMap::SUBJECT_SCOPE, $filters);
    }
    
    
    public function selectRoleTypes(array $filters)
    {
        return $this->selectWhat(iTopicMap::SUBJECT_ASSOCIATION_ROLE_TYPE, $filters);
    }
    
    
    public function selectRolePlayers(array $filters)
    {
        // TODO: Currently not implemented
        return [ ];
    }
    
    
    protected function selectWhat($what, array $filters)
    {
        $what_id = $this->getTopicIdBySubject($what);
        
        if (strlen($what_id) === 0)
        {
            return -1;
        }
        
        // TODO: Implement both "all" and "recent"; currently it's only "all"
        
        if (! isset($filters[ 'get_mode' ]))
        {
            $filters[ 'get_mode' ] = 'all';
        }

        if (! isset($filters[ 'limit' ]))
        {
            $filters[ 'limit' ] = 500;
        }
        
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
        {
            return $ok;
        }

        $query = sprintf
        (
            'MATCH (t%s) RETURN t.id',
            $this->services->db_utils->labelsString([ 'Topic', $what_id ])
        );

        $this->logger->addInfo($query);

        try
        {
            $qresult = $this->services->db->run($query);
        }
        catch (\GraphAware\Neo4j\Client\Exception\Neo4jException $exception)
        {
            $this->logger->addError($exception->getMessage());
            // TODO: Error handling
            return -1;
        }

        $result = [ ];

        foreach ($qresult->getRecords() as $record)
        {
            $result[ ] = $record->get('t.id');
        }

        return $result;
    }
}
