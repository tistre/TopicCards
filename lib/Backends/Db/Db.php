<?php

namespace Xddb\Backends\Db;


class Db extends Core
{
    protected $db = false;
    
    
    public function connect()
    {
        if ($this->db !== false)
            return 0;
        
        $db_params = $this->services->getDbParams();
        
        $this->db = new \PDO
        (
            $db_params[ 'dsn' ], 
            $db_params[ 'username' ], 
            $db_params[ 'password' ],
            $db_params[ 'driver_options' ]
        );

        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        $this->db->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
        $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        
        return 1;
    }


    public function selectTopicIds(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        if (! empty($filters[ 'type' ]))
        {
            $sql = $this->db->prepare(sprintf
            (
                'select distinct type_topic as topic_id from %s_type'
                . ' where type_type = :type_type', 
                $prefix
            ));

            $sql->bindValue(':type_type', $filters[ 'type' ], \PDO::PARAM_STR);
        }
        else
        {
            $sql = $this->db->prepare(sprintf('select topic_id from %s_topic', $prefix));
        }
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $row[ 'topic_id' ];

        return $result;
    }
    
    
    public function selectTopicData(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        $sql = $this->db->prepare(sprintf
        (
            'select * from %s_topic'
            . ' where topic_id = :topic_id', 
            $prefix
        ));

        $sql->bindValue(':topic_id', $filters[ 'id' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
        {
            $row = $this->stripColumnPrefix('topic_', $row);
            
            $row[ 'types' ] = [ ];
            
            foreach ($this->selectTypeData($topicmap, [ 'topic' => $row[ 'id' ] ]) as $type_row)
                $row[ 'types' ][ ] = $type_row[ 'type' ];

            $row[ 'subject_identifiers' ] = [ ];
            $row[ 'subject_locators' ] = [ ];

            foreach ($this->selectSubjectData($topicmap, [ 'topic' => $row[ 'id' ] ]) as $subject_row)
            {
                if (intval($subject_row[ 'islocator' ]) > 0)
                {
                    $row[ 'subject_locators' ][ ] = $subject_row[ 'value' ];
                }
                else
                {
                    $row[ 'subject_identifiers' ][ ] = $subject_row[ 'value' ];
                }
            }
                        
            $row[ 'names' ] = $this->selectNameData($topicmap, [ 'topic' => $row[ 'id' ] ]);

            $row[ 'occurrences' ] = $this->selectOccurrenceData($topicmap, [ 'topic' => $row[ 'id' ] ]);

            $result[ ] = $row;
        }

        return $result;        
    }
    
    
    public function selectNameData(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        $sql = $this->db->prepare(sprintf
        (
            'select * from %s_name'
            . ' where name_topic = :topic_id', 
            $prefix
        ));

        $sql->bindValue(':topic_id', $filters[ 'topic' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        $name_ids = [ ];
        
        foreach ($sql->fetchAll() as $row)
        {
            $row = $this->stripColumnPrefix('name_', $row);
            $row[ 'scope' ] = [ ];
            
            $result[ ] = $row;
            $name_ids[ ] = intval($row[ 'id' ]) ;
        }
        
        $scope_data = $this->selectScopeData($topicmap, [ 'name' => $name_ids ]);
        
        foreach ($scope_data as $scope_row)
        {
            foreach ($result as $key => $row)
            {
                if (intval($row[ 'id' ]) !== intval($scope_row[ 'name' ]))
                    continue;
                    
                $result[ $key ][ 'scope' ][ ] = $scope_row[ 'scope' ];
            }
        }
            
        return $result;
    }
    
    
    public function selectOccurrenceData(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        $sql = $this->db->prepare(sprintf
        (
            'select * from %s_occurrence'
            . ' where occurrence_topic = :topic_id', 
            $prefix
        ));

        $sql->bindValue(':topic_id', $filters[ 'topic' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        $occurrence_ids = [ ];
        
        foreach ($sql->fetchAll() as $row)
        {
            $row = $this->stripColumnPrefix('occurrence_', $row);
            $row[ 'scope' ] = [ ];
            
            $result[ ] = $row;
            $occurrence_ids[ ] = intval($row[ 'id' ]) ;
        }
        
        $scope_data = $this->selectScopeData($topicmap, [ 'occurrence' => $occurrence_ids ]);
        
        foreach ($scope_data as $scope_row)
        {
            foreach ($result as $key => $row)
            {
                if (intval($row[ 'id' ]) !== intval($scope_row[ 'occurrence' ]))
                    continue;
                    
                $result[ $key ][ 'scope' ][ ] = $scope_row[ 'scope' ];
            }
        }
            
        return $result;
    }
    
    
    public function selectAssociationIds(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        if (! empty($filters[ 'type' ]))
        {
            $sql = $this->db->prepare(sprintf
            (
                'select association_id from %s_association'
                . ' where association_type = :association_type', 
                $prefix
            ));

            $sql->bindValue(':association_type', $filters[ 'type' ], \PDO::PARAM_STR);
        }
        else
        {
            $sql = $this->db->prepare(sprintf('select association_id from %s_association', $prefix));
        }
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $row[ 'association_id' ];

        return $result;
    }
    

    public function selectAssociationData(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        $sql = $this->db->prepare(sprintf
        (
            'select * from %s_association'
            . ' where association_id = :association_id', 
            $prefix
        ));

        $sql->bindValue(':association_id', $filters[ 'id' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        $association_ids = [ ];
        
        foreach ($sql->fetchAll() as $row)
        {
            $row = $this->stripColumnPrefix('association_', $row);
            
            $row[ 'roles' ] = $this->selectRoleData($topicmap, [ 'association' => $row[ 'id' ] ]);
            $row[ 'scope' ] = [ ];

            $result[ ] = $row;
            
            $association_ids[ ] = $row[ 'id' ];
        }

        $scope_data = $this->selectScopeData($topicmap, [ 'association' => $association_ids ]);
error_log(print_r($scope_data, true));
        
        foreach ($scope_data as $scope_row)
        {
            foreach ($result as $key => $row)
            {
                if ($row[ 'id' ] !== $scope_row[ 'association' ])
                    continue;
                    
                $result[ $key ][ 'scope' ][ ] = $scope_row[ 'scope' ];
            }
        }

        return $result;        
    }

    
    public function selectScopeData(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        // XXX ugly hack
        
        if (isset($filters[ 'name' ]))
        {
            $where_column = 'name';
        }
        elseif (isset($filters[ 'occurrence' ]))
        {
            $where_column = 'occurrence';
        }
        elseif (isset($filters[ 'association' ]))
        {
            $where_column = 'association';
        }
        else
        {
            return -1;
        }
        
        if (count($filters[ $where_column ]) === 0)
            return array();
            
        $sql_str = sprintf
        (
            'select * from %s_scope'
            . ' where scope_%s in (', 
            $prefix,
            $where_column
        );
        
        $parts = [ ];
        $bind = [ ];
        
        foreach ($filters[ $where_column ] as $i => $name)
        {
            $bind_name = ':val_' . $i;
            $parts[ ] = $bind_name;            
            $bind[ $bind_name ] = $name;
        }
        
        $sql_str .= implode(', ', $parts) . ')';
        
        $sql = $this->db->prepare($sql_str);

        foreach ($bind as $bind_name => $value)
        {
            $datatype = \PDO::PARAM_INT;
            
            if ($where_column === 'association')
                $datatype = \PDO::PARAM_STR;
                
            $sql->bindValue($bind_name, $value, $datatype);
        }
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $this->stripColumnPrefix('scope_', $row);
            
        return $result;
    }


    public function selectTypeData(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        $sql = $this->db->prepare(sprintf
        (
            'select * from %s_type'
            . ' where type_topic = :type_topic', 
            $prefix
        ));
        
        $sql->bindValue(':type_topic', $filters[ 'topic' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $this->stripColumnPrefix('type_', $row);
            
        return $result;
    }
    
    
    public function selectSubjectData(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        $sql = $this->db->prepare(sprintf
        (
            'select * from %s_subject'
            . ' where subject_topic = :subject_topic', 
            $prefix
        ));
        
        $sql->bindValue(':subject_topic', $filters[ 'topic' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $this->stripColumnPrefix('subject_', $row);
            
        return $result;
    }
    
    
    public function selectRoleData(\Xddb\Interfaces\iTopicMap $topicmap, array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $topicmap->getUrl();
        
        $sql = $this->db->prepare(sprintf
        (
            'select * from %s_role'
            . ' where role_association = :role_association', 
            $prefix
        ));
        
        $sql->bindValue(':role_association', $filters[ 'association' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $this->stripColumnPrefix('role_', $row);
            
        return $result;
    }
    
    
    protected function stripColumnPrefix($prefix, array $row)
    {
        $result = [ ];
        $len = strlen($prefix);
        
        foreach ($row as $column => $value)
        {
            if (substr($column, 0, $len) === $prefix)
                $column = substr($column, $len);
                
            $result[ $column ] = $value;
        }
        
        return $result;
    }
}
