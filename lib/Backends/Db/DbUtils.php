<?php

namespace Xddb\Backends\Db;


class DbUtils extends Core
{
    public function connect()
    {
        if ($this->services->db !== false)
            return 0;
        
        $db_params = $this->services->getDbParams();
        
        $this->services->db = new \PDO
        (
            $db_params[ 'dsn' ], 
            $db_params[ 'username' ], 
            $db_params[ 'password' ],
            $db_params[ 'driver_options' ]
        );

        $this->services->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        $this->services->db->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
        $this->services->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        
        return 1;
    }


    public function datetimeToDb($date)
    {
        // "2004-02-12T15:19:21+00:00" => "2004-02-12 15:19:21"
        // XXX hacked
        
        return str_replace('T', ' ', substr($date, 0, 19));
    }
    
    
    public function prepareSelectSql($table, $what, array $where, $postfix = '')
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;

        $this->columnValueStatements($where, '', $stmts, $bind);
        
        $sql = $this->services->db->prepare(sprintf
        (
            'select %s from %s where (%s) %s', 
            $what,
            $table,
            implode(' and ', $stmts),
            $postfix
        ));

        foreach ($bind as $value)
            $sql->bindValue($value[ 'bind_param' ], $value[ 'value' ], $value[ 'datatype' ]);
            
        return $sql;
    }
    

    protected function columnValueStatements($values, $bind_post, &$stmts, &$bind)
    {
        $stmts = [ ];
        $bind = [ ];
        
        foreach ($values as $key => $value)
        {
            if (! isset($value[ 'bind_param' ]))
                $value[ 'bind_param' ] = ':' . $value[ 'column' ];
                
            $value[ 'bind_param' ] .= $bind_post;

            if (! isset($value[ 'datatype' ]))
                $value[ 'datatype' ] = \PDO::PARAM_STR;

            if (strlen($value[ 'value' ]) === 0)
            {
                // Ugly PDO hack; why can't I use \PDO::PARAM_NULL? See:
                // http://stackoverflow.com/questions/1391777/how-do-i-insert-null-values-using-pdo
                $value[ 'value' ] = null;
                $value[ 'datatype' ] = \PDO::PARAM_INT;
            }
                
            $stmts[ ] = sprintf('%s=%s', $value[ 'column' ], $value[ 'bind_param' ]);
            
            $bind[ ] = 
            [
                'bind_param' => $value[ 'bind_param' ],
                'value' => $value[ 'value' ],
                'datatype' => $value[ 'datatype' ]
            ];
        }
    }
    
    
    public function prepareInsertSql($table, array $values)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        foreach ($values as $key => $value)
        {
            if (! isset($value[ 'bind_param' ]))
                $values[ $key ][ 'bind_param' ] = ':' . $value[ 'column' ];

            if (! isset($value[ 'datatype' ]))
                $values[ $key ][ 'datatype' ] = \PDO::PARAM_STR;
                
            if (strlen($value[ 'value' ]) === 0)
            {
                // Ugly PDO hack; why can't I use \PDO::PARAM_NULL? See:
                // http://stackoverflow.com/questions/1391777/how-do-i-insert-null-values-using-pdo
                $values[ $key ][ 'value' ] = null;
                $values[ $key ][ 'datatype' ] = \PDO::PARAM_INT;
            }
        }
        
        $sql = $this->services->db->prepare(sprintf
        (
            'insert into %s (%s) values (%s)', 
            $table, 
            implode(', ', array_column($values, 'column')),
            implode(', ', array_column($values, 'bind_param'))
        ));

        foreach ($values as $value)
            $sql->bindValue($value[ 'bind_param' ], $value[ 'value' ], $value[ 'datatype' ]);
            
        return $sql;
    }
    

    public function prepareUpdateSql($table, array $set, array $where)
    {
        if (count($where) === 0)
            return -1;
            
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;

        $this->columnValueStatements($set, '_s', $set_stmts, $set_bind);
        $this->columnValueStatements($where, '_w', $where_stmts, $where_bind);
        
        $sql = $this->services->db->prepare(sprintf
        (
            'update %s set %s where (%s)', 
            $table, 
            implode(', ', $set_stmts),
            implode(' and ', $where_stmts)
        ));

        foreach (array_merge($set_bind, $where_bind) as $value)
            $sql->bindValue($value[ 'bind_param' ], $value[ 'value' ], $value[ 'datatype' ]);
            
        return $sql;
    }
    

    public function prepareDeleteSql($table, array $where)
    {
        if (count($where) === 0)
            return -1;
            
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;

        $this->columnValueStatements($where, '', $stmts, $bind);
        
        $sql = $this->services->db->prepare(sprintf
        (
            'delete from %s where (%s)', 
            $table, 
            implode(' and ', $stmts)
        ));

        foreach ($bind as $value)
            $sql->bindValue($value[ 'bind_param' ], $value[ 'value' ], $value[ 'datatype' ]);
            
        return $sql;
    }
    

    public function selectTopicIds(array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
        if (! empty($filters[ 'type' ]))
        {
            $sql = $this->services->db->prepare(sprintf
            (
                'select distinct type_topic as topic_id from %s_type'
                . ' where type_type = :type_type', 
                $prefix
            ));

            $sql->bindValue(':type_type', $filters[ 'type' ], \PDO::PARAM_STR);
        }
        else
        {
            $sql = $this->services->db->prepare(sprintf('select topic_id from %s_topic', $prefix));
        }
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $row[ 'topic_id' ];

        return $result;
    }
        
    
    public function selectAssociationIds(array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
        if (! empty($filters[ 'type' ]))
        {
            $sql = $this->services->db->prepare(sprintf
            (
                'select association_id from %s_association'
                . ' where association_type = :association_type', 
                $prefix
            ));

            $sql->bindValue(':association_type', $filters[ 'type' ], \PDO::PARAM_STR);
        }
        else
        {
            $sql = $this->services->db->prepare(sprintf('select association_id from %s_association', $prefix));
        }
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $row[ 'association_id' ];

        return $result;
    }
    

    public function selectAssociationData(array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
        $sql = $this->services->db->prepare(sprintf
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
            
            $row[ 'roles' ] = $this->selectRoleData([ 'association' => $row[ 'id' ] ]);
            $row[ 'scope' ] = [ ];

            $result[ ] = $row;
            
            $association_ids[ ] = $row[ 'id' ];
        }

        $scope_data = $this->selectScopeData([ 'association' => $association_ids ]);
        
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

    
    public function selectScopeData(array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
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
        
        $sql = $this->services->db->prepare($sql_str);

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


    public function insertTypeData(array $types_data)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $values = [ ];
        
        foreach ($types_data as $data)
        {
            foreach ($data as $key => $value)
            {
                $values[ ] =
                [
                    'column' => 'type_' . $key,
                    'value' => $value
                ];
            }
        }
        
        $sql = $this->prepareInsertSql($this->services->topicmap->getUrl() . '_type', $values);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        return 1;
    }
    
    
    public function insertSubjectData(array $subjects_data)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $values = [ ];
        
        foreach ($subjects_data as $data)
        {
            foreach ($data as $key => $value)
            {
                $values[ ] =
                [
                    'column' => 'subject_' . $key,
                    'value' => $value,
                    'datatype' => ($key === 'islocator' ? \PDO::PARAM_INT: \PDO::PARAM_STR)
                ];
            }
        }
        
        $sql = $this->prepareInsertSql($this->services->topicmap->getUrl() . '_subject', $values);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        return 1;
    }
    
    
    public function selectRoleData(array $filters)
    {
        $ok = $this->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
        $sql = $this->services->db->prepare(sprintf
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
    
    
    public function stripColumnPrefix($prefix, array $row)
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
