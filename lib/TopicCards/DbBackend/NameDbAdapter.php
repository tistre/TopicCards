<?php

namespace TopicCards\DbBackend;


trait NameDbAdapter
{
    public function selectAll(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        if (! empty($filters[ 'reifier' ]))
        {
            // TODO to be implemented
            return -1;
        }

        if (! isset($filters[ 'topic' ]))
        {
            return -1;
        }
        
        $query = 'MATCH (t:Topic { id: {id} })-[:hasName]->(node:Name) RETURN node';
        $bind = [ 'id' => $filters[ 'topic' ] ];

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
            $node = $record->get('node');
            
            $row =
                [
                    'id' => ($node->hasValue('id') ? $node->value('id') : false),
                    'value' => ($node->hasValue('value') ? $node->value('value') : false),
                    'scope' => [ ]
                ];
            
            // Type

            $types = array_values(array_diff($node->labels(), [ 'Name' ]));
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
            
            $result[ ] = $row;
        }
        
        return $result;
    }


    public function insertAll($topic_id, array $data, \GraphAware\Neo4j\Client\Transaction\Transaction $transaction)
    {
        foreach ($data as $name_data)
        {
            $this->insertName($topic_id, $name_data, $transaction);
        }

        // TODO: error handling
        
        return 1;
    }
    
    
    public function updateAll($topic_id, array $data, array $previous_data, \GraphAware\Neo4j\Client\Transaction\Transaction $transaction)
    {
        foreach ($data as $name_data)
        {
            // No ID? Must be a new name
            
            if (empty($name_data[ 'id' ]))
            {
                $ok = $this->insertName($topic_id, $name_data, $transaction);
                
                if ($ok < 0)
                {
                    return $ok;
                }
                
                continue;
            }
            
            // If the ID is not in $previous_data, it's a new name
            
            $found = false;
            
            foreach ($previous_data as $previous_name_data)
            {
                if ($previous_name_data[ 'id' ] === $name_data[ 'id' ])
                {
                    $found = true;
                    break;
                }
            }
            
            if (! $found)
            {
                $ok = $this->insertName($topic_id, $name_data, $transaction);
                
                if ($ok < 0)
                {
                    return $ok;
                }
                
                continue;
            }

            // It's an updated name...

            $ok = $this->updateName($topic_id, $name_data, $previous_name_data, $transaction);
            
            if ($ok < 0)
            {
                return $ok;
            }
            
            // TODO: handle name deletion, or empty value
        }
        
        // TODO: error handling
        return $ok;
    }


    protected function insertName($topic_id, array $data, \GraphAware\Neo4j\Client\Transaction\Transaction $transaction)
    {
        if ((! isset($data[ 'value' ])) || (strlen($data[ 'value' ]) === 0))
        {
            return 0;
        }

        if (empty($data[ 'type' ]))
        {
            return -1;
        }

        if (empty($data[ 'id' ]))
        {
            $data[ 'id' ] = $this->getTopicMap()->createId();
        }

        if (empty($data[ 'scope' ]))
        {
            $data[ 'scope' ] = [ ];
        }
        elseif (! is_array($data[ 'scope' ]))
        {
            $data[ 'scope' ] = [ $data[ 'scope' ] ];
        }

        $property_data =
            [
                'id' => $data[ 'id' ],
                'value' => $data[ 'value' ],
                'scope' => $data[ 'scope' ]
            ];

        $bind = [ 'topic_id' => $topic_id ];

        $property_query = $this->services->db_utils->propertiesString($property_data, $bind);

        $classes = [ 'Name' , $data[ 'type' ] ];

        $query = sprintf
        (
            'MATCH (a:Topic { id: {topic_id} })'
            . ' CREATE (a)-[:hasName]->(b%s { %s })',
            $this->services->db_utils->labelsString($classes),
            $property_query
        );
        
        $this->logger->addInfo($query, $bind);

        $transaction->push($query, $bind);

        // TODO: error handling
        return 1;
    }
    
    
    protected function updateName($topic_id, array $data, array $previous_data, \GraphAware\Neo4j\Client\Transaction\Transaction $transaction)
    {
        if ((! isset($data[ 'value' ])) || (strlen($data[ 'value' ]) === 0))
        {
            $bind = [ 'id' => $data[ 'id' ] ];
            $query = 'MATCH (node:Name { id: {id} }) OPTIONAL MATCH (node)-[r:hasName]-() DELETE r, node';

            $this->logger->addInfo($query, $bind);
            
            $transaction->push($query, $bind);

            // TODO: error handling
            return 1;
        }

        if (empty($data[ 'scope' ]))
        {
            $data[ 'scope' ] = [ ];
        }
        elseif (! is_array($data[ 'scope' ]))
        {
            $data[ 'scope' ] = [ $data[ 'scope' ] ];
        }

        $property_data = [ ];

        foreach ([ 'value', 'scope' ] as $key)
        {
            // Skip unmodified values

            if (isset($previous_data[ $key ]) && (serialize($previous_data[ $key ]) === serialize($data[ $key ])))
            {
                continue;
            }

            $property_data[ $key ] = $data[ $key ];
        }
        
        $bind = [ 'id' => $data[ 'id' ] ];
        $property_query = $this->services->db_utils->propertiesUpdateString('node', $property_data, $bind);

        // Skip update if no property changes and no type change!
        $dirty = (strlen($property_query) > 0);

        $query = sprintf
        (
            'MATCH (node:Name { id: {id} })%s',
            $property_query
        );

        if ($data[ 'type' ] !== $previous_data[ 'type' ])
        {
            $query .= sprintf
            (
                ' REMOVE node%s',
                $this->services->db_utils->labelsString([ $previous_data[ 'type' ] ])
            );

            $query .= sprintf
            (
                ' SET node%s',
                $this->services->db_utils->labelsString([ $data[ 'type' ] ])
            );
            
            $dirty = true;
        }

        if (! $dirty)
        {
            return 0;
        }

        $this->logger->addInfo($query, $bind);

        $transaction->push($query, $bind);

        // TODO: error handling
        return 1;
    }
}
