<?php

namespace TopicCards\DbBackend;

use \TopicCards\iAssociation;
use TopicCards\iTopicMap;


trait AssociationDbAdapter
{
    public function selectAll(array $filters)
    {
        $ok = $this->services->db_utils->connect();

        if ($ok < 0)
            return $ok;

        $query = 'MATCH (node:Association { id: {id} }) RETURN node';
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

        $role = new Role($this->services, $this->topicmap);

        foreach ($qresult->records() as $record)
        {
            $node = $record->get('node');

            $row =
                [
                    'created' => $node->value('created'),
                    'id' => $node->value('id'),
                    'updated' => $node->value('updated'),
                    'version' => $node->value('version'),
                    'scope' => [ ],
                ];

            // Type

            $types = array_values(array_diff($node->labels(), [ 'Association' ]));
            $row[ 'type' ] = $types[ 0 ];

            // Scope

            if ($node->hasValue('scope'))
            {
                $row[ 'scope' ] = $node->value('scope');

                if (! is_array($row[ 'scope' ]))
                {
                    $value = $row[ 'scope' ];
                    $row[ 'scope' ] = [ ];

                    if (strlen($value) > 0)
                    {
                        $row[ 'scope' ][ ] = $value;
                    }
                }
            }

            $row[ 'roles' ] = $role->selectAll([ 'association' => $row[ 'id' ] ]);

            $result[ ] = $row;
        }

        return $result;
    }
    

    public function insertAll(array $data)
    {
        $ok = $this->services->db_utils->connect();

        if ($ok < 0)
            return $ok;

        $now = date('c');

        $data[ 'created' ] = $data[ 'updated' ] = $now;
        $data[ 'version' ] = 1;

        if (empty($data[ 'scope' ]))
        {
            $data[ 'scope' ] = [ ];
        }
        elseif (! is_array($data[ 'scope' ]))
        {
            $data[ 'scope' ] = [ $data[ 'scope' ] ];
        }

        $property_data = [ ];

        foreach ([ 'created', 'id', 'updated', 'version', 'scope' ] as $key)
        {
            $property_data[ $key ] = $data[ $key ];
        }

        $bind = [ ];
        $property_query = $this->services->db_utils->propertiesString($property_data, $bind);

        $classes = [ 'Association', $data[ 'type' ] ];

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
            [ $data[ 'type' ] ],
            iTopicMap::SUBJECT_ASSOCIATION_TYPE
        );

        $type_queries = array_merge($type_queries, $this->services->db_utils->tmConstructLabelQueries
        (
            $this->topicmap,
            $data[ 'scope' ],
            iTopicMap::SUBJECT_SCOPE
        ));

        foreach ($type_queries as $type_query)
        {
            $this->logger->addInfo($type_query['query'], $type_query['bind']);
            $transaction->push($type_query['query'], $type_query['bind']);
        }

        // TODO: Error handling

        $role = new Role($this->services, $this->topicmap);
        $ok = $role->insertAll($data[ 'id' ], $data[ 'roles' ], $transaction);

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
                iAssociation::EVENT_SAVING, 
                [ 'association' => $this, 'dml' => 'insert' ],
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

        if (empty($data[ 'scope' ]))
        {
            $data[ 'scope' ] = [ ];
        }
        elseif (! is_array($data[ 'scope' ]))
        {
            $data[ 'scope' ] = [ $data[ 'scope' ] ];
        }

        $property_data = [ ];

        foreach ([ 'created', 'id', 'updated', 'version', 'scope' ] as $key)
        {
            // Skip unmodified values

            if (isset($this->previous_data[ $key ]) && (serialize($this->previous_data[ $key ]) === serialize($data[ $key ])))
            {
                continue;
            }

            $property_data[ $key ] = $data[ $key ];
            
            if ($key === 'scope')
            {
                // Mark type topics

                $type_queries = $this->services->db_utils->tmConstructLabelQueries
                (
                    $this->topicmap,
                    $data[ $key ],
                    iTopicMap::SUBJECT_SCOPE
                );

                foreach ($type_queries as $type_query)
                {
                    $this->logger->addInfo($type_query['query'], $type_query['bind']);
                    $transaction->push($type_query['query'], $type_query['bind']);
                }
            }
        }

        $bind = [ 'id' => $data[ 'id' ] ];
        $property_query = $this->services->db_utils->propertiesUpdateString('node', $property_data, $bind);

        $query = sprintf
        (
            'MATCH (node:Association { id: {id} })%s',
            $property_query
        );

        if ($this->previous_data[ 'type' ] !== $data[ 'type' ])
        {
            $query .= sprintf
            (
                ' REMOVE node%s',
                $this->services->db_utils->labelsString([ $this->previous_data[ 'type' ] ])
            );

            $query .= sprintf
            (
                ' SET node%s',
                $this->services->db_utils->labelsString([ $data[ 'type' ] ])
            );

            // Mark type topics

            $type_queries = $this->services->db_utils->tmConstructLabelQueries
            (
                $this->topicmap,
                [ $data[ 'type' ] ],
                iTopicMap::SUBJECT_ASSOCIATION_ROLE_TYPE
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
            $role = new Role($this->services, $this->topicmap);

            $ok = $role->updateAll
            (
                $data[ 'id' ],
                $data[ 'roles' ],
                $this->previous_data[ 'roles' ],
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
                iAssociation::EVENT_SAVING,
                [ 'association' => $this, 'dml' => 'update' ],
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
            'MATCH (node:Association { id: {id} })'
            . ' OPTIONAL MATCH (node)-[r]-()'
            . ' DELETE r, node';

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
                iAssociation::EVENT_DELETING, 
                [ 'association_id' => $id ],
                $callback_result
            );

            if (isset($callback_result[ 'index_related' ]))
                $this->addIndexRelated($callback_result[ 'index_related' ]);
        }
            
        return 1;
    }
}
