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
    

    protected function prepareBindValue(array $value)
    {
        if (! isset($value[ 'datatype' ]))
            $value[ 'datatype' ] = \PDO::PARAM_STR;

        if (strlen($value[ 'value' ]) === 0)
        {
            // Ugly PDO hack; why can't I use \PDO::PARAM_NULL? See:
            // http://stackoverflow.com/questions/1391777/how-do-i-insert-null-values-using-pdo
            $value[ 'value' ] = null;
            $value[ 'datatype' ] = \PDO::PARAM_INT;
        }
        
        return $value;
    }
    
    
    // XXX  add class name to $sql
    public function bindValues($sql, array $values)
    {
        foreach ($values as $value)
        {
            $value = $this->prepareBindValue($value);
            
            $sql->bindValue($value[ 'bind_param' ], $value[ 'value' ], $value[ 'datatype' ]);
        }
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

            $value = $this->prepareBindValue($value);
                
            $stmts[ ] = sprintf('%s=%s', $value[ 'column' ], $value[ 'bind_param' ]);
            
            $bind[ ] = 
            [
                'bind_param' => $value[ 'bind_param' ],
                'value' => $value[ 'value' ],
                'datatype' => $value[ 'datatype' ]
            ];
        }
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

        $this->bindValues($sql, $bind);
            
        return $sql;
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

            $values[ $key ] = $this->prepareBindValue($values[ $key ]);
        }
        
        $sql = $this->services->db->prepare(sprintf
        (
            'insert into %s (%s) values (%s)', 
            $table, 
            implode(', ', array_column($values, 'column')),
            implode(', ', array_column($values, 'bind_param'))
        ));

        $this->bindValues($sql, $values);
            
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

        $this->bindValues($sql, array_merge($set_bind, $where_bind));

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

        $this->bindValues($sql, $bind);
            
        return $sql;
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
