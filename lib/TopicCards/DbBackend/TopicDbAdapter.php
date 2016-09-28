<?php

namespace TopicCards\DbBackend;

use \TopicCards\iTopic;
use TopicCards\iTopicMap;


trait TopicDbAdapter
{
    public function selectAll(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;

        $query = 'MATCH (node:Topic { id: {id} }) RETURN node';
        $bind = [ 'id' => $filters[ 'id' ] ];

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

        // TODO add error handling

        $result = [ ];
        
        $name = new Name($this->services, $this->topicmap);
        $occurrence = new Occurrence($this->services, $this->topicmap);

        foreach ($qresult->records() as $record)
        {
            $node = $record->get('node');
            
            $row =
                [
                    'created' => $node->value('created'),
                    'id' => $node->value('id'),
                    'updated' => $node->value('updated'),
                    'version' => $node->value('version'),
                    'types' => array_values(array_diff($node->labels(), [ 'Topic' ])),
                    'subject_identifiers' => [ ],
                    'subject_locators' => [ ],
                    'reifies_what' => ($node->hasValue('reifies_what') ? $node->value('reifies_what') : iTopic::REIFIES_NONE),
                    'reifies_id' => ($node->hasValue('reifies_id') ? $node->value('reifies_id') : '')
                ];

            // Subjects
            
            foreach ([ 'subject_identifiers', 'subject_locators' ] as $key)
            {
                if (! $node->hasValue($key))
                {
                    continue;
                }
                
                $values = $node->value($key);
                
                if (! is_array($values))
                {
                    $value = $values;
                    $values = [ ];
                    
                    if (strlen($value) > 0)
                    {
                        $values[ ] = $value;
                    }
                }
                
                $row[ $key ] = $values;
            }

            $row[ 'names' ] = $name->selectAll([ 'topic' => $row[ 'id' ] ]);

            $row[ 'occurrences' ] = $occurrence->selectAll([ 'topic' => $row[ 'id' ] ]);

            $result[ ] = $row;
        }

        return $result;        
    }
    

    public function selectReifiedObject($reifies_what)
    {
        $result = false;
        
        $map =
        [
            iTopic::REIFIES_NAME => 'Name',
            iTopic::REIFIES_OCCURRENCE => 'Occurrence',
            iTopic::REIFIES_ASSOCIATION => 'Association',
            iTopic::REIFIES_ROLE => 'Role'
        ];
        
        if (! isset($map[ $reifies_what ]))
            return false;
        
        $method = 'selectReifiedObject_' . $map[ $reifies_what ];
        
        return $this->$method();
    }
    
    
    protected function selectReifiedObject_Name()
    {
        $name = new Name($this->services, $this->topicmap);

        $rows = $name->selectAll([ 'reifier' => $this->id ]);
    
        if (count($rows) === 0)
            return false;

        $topic = new Topic($this->services, $this->topicmap);
        $ok = $topic->load($rows[ 0 ][ 'topic' ]);
        
        if ($ok < 0)
            return false;

        foreach ($topic->getNames([ 'reifier' => $this->id ]) as $name)
        {
            if ($name->getId() !== $rows[ 0 ][ 'id' ])
                continue;
                
            return
            [
                'topic' => $topic,
                'name' => $name
            ];
        }
        
        return false;
    }
    
    
    protected function selectReifiedObject_Occurrence()
    {
        $occurrence = new Occurrence($this->services, $this->topicmap);

        $rows = $occurrence->selectAll([ 'reifier' => $this->id ]);
    
        if (count($rows) === 0)
            return false;

        $topic = new Topic($this->services, $this->topicmap);
        $ok = $topic->load($rows[ 0 ][ 'topic' ]);
        
        if ($ok < 0)
            return false;

        foreach ($topic->getOccurrences([ ]) as $occurrence)
        {
            if ($occurrence->getId() !== $rows[ 0 ][ 'id' ])
                continue;
                
            return
            [
                'topic' => $topic,
                'occurrence' => $occurrence
            ];
        }
        
        return false;
    }
    
    
    protected function selectReifiedObject_Association()
    {
        $association = new Association($this->services, $this->topicmap);

        $rows = $association->selectAll([ 'reifier' => $this->id ]);
    
        if (count($rows) === 0)
            return false;

        $ok = $association->load($rows[ 0 ][ 'id' ]);
        
        if ($ok < 0)
            return false;

        return
        [
            'association' => $association,
        ];
    }
    
    
    protected function selectReifiedObject_Role()
    {
        $role = new Role($this->services, $this->topicmap);

        $rows = $role->selectAll([ 'reifier' => $this->id ]);
    
        if (count($rows) === 0)
            return false;

        $association = new Association($this->services, $this->topicmap);
        $ok = $association->load($rows[ 0 ][ 'association' ]);
        
        if ($ok < 0)
            return false;

        foreach ($association->getRoles() as $role)
        {
            if ($role->getId() !== $rows[ 0 ][ 'id' ])
                continue;
                
            return
            [
                'association' => $association,
                'role' => $role
            ];
        }
        
        return false;
    }
    
    
    public function insertAll(array $data)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;

        $now = date('c');
        
        $data[ 'created' ] = $data[ 'updated' ] = $now; 
        $data[ 'version' ] = 1;

        $property_data = [ ];

        foreach ([ 'created', 'id', 'reifies_id', 'reifies_what', 'subject_identifiers', 'subject_locators', 'updated', 'version' ] as $key)
        {
            $property_data[ $key ] = $data[ $key ];
        }
        
        $bind = [ ];
        $property_query = $this->services->db_utils->propertiesString($property_data, $bind);

        $classes = array_merge([ 'Topic' ], $data[ 'types' ]);

        $this->services->db_utils->beginTransaction($transaction);
        
        $query = sprintf
        (
            'CREATE (n%s { %s })',
            $this->services->db_utils->labelsString($classes),
            $property_query
        );

        $this->logger->addInfo($query, $bind);
        
        $transaction->push($query, $bind);
        
        // Mark type topics

        $type_queries = $this->services->db_utils->tmConstructLabelQueries
        (
            $this->topicmap,
            $data[ 'types' ], 
            iTopicMap::SUBJECT_TOPIC_TYPE
        );
        
        foreach ($type_queries as $type_query)
        {
            $this->logger->addInfo($type_query['query'], $type_query['bind']);
            $transaction->push($type_query['query'], $type_query['bind']);
        }

        // TODO: Error handling
        
        $ok = 1;

        if ($ok >= 0)
        {
            $name = new Name($this->services, $this->topicmap);
            $ok = $name->insertAll($data[ 'id' ], $data[ 'names' ], $transaction);
        }

        if ($ok >= 0)
        {
            $occurrence = new Occurrence($this->services, $this->topicmap);
            $ok = $occurrence->insertAll($data[ 'id' ], $data[ 'occurrences' ], $transaction);
        }

        try
        {
            $this->services->db_utils->commit($transaction);
        }
        catch (\GraphAware\Neo4j\Client\Exception\Neo4jException $exception)
        {
            $this->logger->addError($exception->getMessage());
            // TODO: Error handling
            $ok = -1;
        }

        // TODO: Error handling

        if ($ok >= 0)
        {
            $callback_result = [ ];
            
            $ok = $this->topicmap->trigger
            (
                iTopic::EVENT_SAVING, 
                [ 'topic' => $this, 'dml' => 'insert' ],
                $callback_result
            );
            
            if (isset($callback_result[ 'index_related' ]))
                $this->addIndexRelated($callback_result[ 'index_related' ]);
        }
            
        return $ok;
    }


    public function updateAll(array $data)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $data[ 'updated' ] = date('c');        
        $data[ 'version' ]++;

        $property_data = [ ];

        foreach ([ 'created', 'id', 'reifies_id', 'reifies_what', 'subject_identifiers', 'subject_locators', 'updated', 'version' ] as $key)
        {
            // Skip unmodified values
            
            if (isset($this->previous_data[ $key ]) && (serialize($this->previous_data[ $key ]) === serialize($data[ $key ])))
            {
                continue;
            }
            
            $property_data[ $key ] = $data[ $key ];
        }
        
        $bind = [ 'id' => $data[ 'id' ] ];
        $property_query = $this->services->db_utils->propertiesUpdateString('node', $property_data, $bind);

        $query = sprintf
        (
            'MATCH (node:Topic { id: {id} })%s',
            $property_query
        );

        if (isset($this->previous_data[ 'types' ]) && is_array($this->previous_data[ 'types' ]))
        {
            $previous_types = $this->previous_data[ 'types' ];
        }
        else
        {
            $previous_types = [ ];
        }

        $added_types = array_diff($data[ 'types' ], $previous_types);
        $removed_types = array_diff($previous_types, $data[ 'types' ]);

        if (count($removed_types) > 0)
        {
            $query .= sprintf
            (
                ' REMOVE node%s',
                $this->services->db_utils->labelsString($removed_types)
            );
        }

        if (count($added_types) > 0)
        {
            $query .= sprintf
            (
                ' SET node%s',
                $this->services->db_utils->labelsString($added_types)
            );
            
            // Mark type topics

            $type_queries = $this->services->db_utils->tmConstructLabelQueries
            (
                $this->topicmap,
                $added_types,
                iTopicMap::SUBJECT_TOPIC_TYPE
            );

            foreach ($type_queries as $type_query)
            {
                $this->logger->addInfo($type_query['query'], $type_query['bind']);
                $transaction->push($type_query['query'], $type_query['bind']);
            }
        }

        $this->logger->addInfo($query, $bind);

        $this->services->db_utils->beginTransaction($transaction);

        $transaction->push($query, $bind);

        // TODO: Error handling
        $ok = 1;

        if ($ok >= 0)
        {
            $name = new Name($this->services, $this->topicmap);
            
            $ok = $name->updateAll
            (
                $data[ 'id' ], 
                $data[ 'names' ], 
                $this->previous_data[ 'names' ], 
                // Collect an array of queries instead of passing the transaction?
                $transaction
            );
        }

        if ($ok >= 0)
        {
            $occurrence = new Occurrence($this->services, $this->topicmap);

            $ok = $occurrence->updateAll
            (
                $data[ 'id' ],
                $data[ 'occurrences' ],
                $this->previous_data[ 'occurrences' ],
                // Collect an array of queries instead of passing the transaction?
                $transaction
            );
        }

        $ok = 1;

        try
        {
            $this->services->db_utils->commit($transaction);
        }
        catch (\GraphAware\Neo4j\Client\Exception\Neo4jException $exception)
        {
            $this->logger->addError($exception->getMessage());
            // TODO: Error handling
            $ok = -1;
        }
        
        if ($ok >= 0)
        {
            $callback_result = [ ];

            $ok = $this->topicmap->trigger
            (
                iTopic::EVENT_SAVING, 
                [ 'topic' => $this, 'dml' => 'update' ],
                $callback_result
            );

            if (isset($callback_result[ 'index_related' ]))
                $this->addIndexRelated($callback_result[ 'index_related' ]);
        }

        return $ok;
    }


    public function deleteById($id, $version)
    {
        // TODO: Implement $version
        
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;

        $query = 
            'MATCH (node:Topic { id: {id} })'
            . ' OPTIONAL MATCH (node)-[rn:hasName]-(nn:Name)'
            . ' OPTIONAL MATCH (node)-[ro:hasOccurrence]-(no:Occurrence)'
            . ' DELETE nn, rn, no, ro, node';

        $bind = [ 'id' => $id ];

        $this->logger->addInfo($query, $bind);

        try
        {
            $this->services->db->run($query, $bind);
        }
        catch (\GraphAware\Neo4j\Client\Exception\Neo4jException $exception)
        {
            $this->logger->addError($exception->getMessage());
            // TODO: Error handling
            $ok = -1;
        }
        
        // TODO: error handling

        if ($ok >= 0)              
        {  
            $callback_result = [ ];

            $ok = $this->topicmap->trigger
            (
                iTopic::EVENT_DELETING, 
                [ 'topic_id' => $id ],
                $callback_result
            );

            if (isset($callback_result[ 'index_related' ]))
                $this->addIndexRelated($callback_result[ 'index_related' ]);
        }
            
        return 1;
    }
}
