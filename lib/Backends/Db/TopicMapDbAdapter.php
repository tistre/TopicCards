<?php

namespace TopicBank\Backends\Db;


trait TopicMapDbAdapter
{
    public function selectTopics(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->getUrl();
        
        if ((! empty($filters[ 'name_like' ])) && (! empty($filters[ 'type' ])))
        {
            $sql = $this->services->db->prepare(sprintf
            (
                'select distinct name_topic as topic_id from %s_name, %s_type'
                . ' where lower(name_value) like lower(:name_value)'
                . ' and type_type = :type_type'
                . ' and type_topic = name_topic', 
                $prefix, $prefix
            ));

            $sql->bindValue(':name_value', $filters[ 'name_like' ], \PDO::PARAM_STR);
            $sql->bindValue(':type_type', $filters[ 'type' ], \PDO::PARAM_STR);
        }
        elseif (! empty($filters[ 'type' ]))
        {
            $sql = $this->services->db->prepare(sprintf
            (
                'select distinct type_topic as topic_id from %s_type'
                . ' where type_type = :type_type', 
                $prefix
            ));

            $sql->bindValue(':type_type', $filters[ 'type' ], \PDO::PARAM_STR);
        }
        elseif (! empty($filters[ 'name_like' ]))
        {
            $sql = $this->services->db->prepare(sprintf
            (
                'select distinct name_topic as topic_id from %s_name'
                . ' where lower(name_value) like lower(:name_value)', 
                $prefix
            ));

            $sql->bindValue(':name_value', $filters[ 'name_like' ], \PDO::PARAM_STR);
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
    
    
    public function selectTopicBySubjectIdentifier($uri)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return false;
        
        $sql = $this->services->db->prepare(sprintf
        (
            'select subject_topic as topic_id from %s_subject'
            . ' where subject_value = :subject_value'
            // XXX "limit" MySQL specific? Does PDO have a better way?
            . ' limit 1', 
            $this->getUrl()
        ));

        $sql->bindValue(':subject_value', $uri, \PDO::PARAM_STR);

        $ok = $sql->execute();
        
        if ($ok === false)
            return false;

        foreach ($sql->fetchAll() as $row)
            return $row[ 'topic_id' ];

        return false;
    }
    
    
    public function selectAssociations(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->getUrl();

        $sql_str = sprintf('select association_id from %s_association', $prefix);
        
        $where = [ ];
        $bind = [ ];

        if (! empty($filters[ 'type' ]))
        {
            $where[ ] = 'association_type = :association_type';
            
            $bind[ ] = 
            [
                'bind_param' => ':association_type', 
                'value' => $filters[ 'type' ] 
            ];
        }

        if (! empty($filters[ 'role_player' ]))
        {
            $where[ ] = sprintf
            (
                'exists (select role_id from %s_role where role_player = :role_player'
                . ' and role_association = association_id)',
                $prefix
            );
            
            $bind[ ] = 
            [
                'bind_param' => ':role_player', 
                'value' => $filters[ 'role_player' ]
            ];
        }

        if (count($where) > 0)
            $sql_str .= ' where ' . implode(' and ', $where);

        $sql = $this->services->db->prepare($sql_str);
        
        $this->services->db_utils->bindValues($sql, $bind);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
            $result[ ] = $row[ 'association_id' ];

        return $result;
    }
    
    
    public function selectTopicTypes(array $filters)
    {
        return $this->selectWhat('type', 'type_type', $filters);
    }
    
    
    public function selectNameTypes(array $filters)
    {
        return $this->selectWhat('name', 'name_type', $filters);
    }
    

    public function selectNameScopes(array $filters)
    {
        // XXX selects all scopes, not just name scopes
        return $this->selectWhat('scope', 'scope_scope', $filters);
    }
    
    
    public function selectOccurrenceTypes(array $filters)
    {
        return $this->selectWhat('occurrence', 'occurrence_type', $filters);
    }


    public function selectOccurrenceDatatypes(array $filters)
    {
        return $this->selectWhat('occurrence', 'occurrence_datatype', $filters);
    }

    
    public function selectOccurrenceScopes(array $filters)
    {
        // XXX selects all scopes, not just occurrence scopes
        return $this->selectWhat('scope', 'scope_scope', $filters);
    }
    
    
    public function selectAssociationTypes(array $filters)
    {
        return $this->selectWhat('association', 'association_type', $filters);
    }
    
    
    public function selectAssociationScopes(array $filters)
    {
        // XXX selects all scopes, not just association scopes
        return $this->selectWhat('scope', 'scope_scope', $filters);
    }
    
    
    public function selectRoleTypes(array $filters)
    {
        return $this->selectWhat('role', 'role_type', $filters);
    }
    
    
    public function selectRolePlayers(array $filters)
    {
        return $this->selectWhat('topic', 'topic_id', $filters);
    }
    
    
    protected function selectWhat($table, $column, array $filters)
    {
        if (! isset($filters[ 'get_mode' ]))
            $filters[ 'get_mode' ] = 'all';
            
        $method = 'selectWhat_' . $filters[ 'get_mode' ];
        
        return $this->$method($table, $column, $filters);
    }
    
    
    protected function selectWhat_recent($table, $column, array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->getUrl();
        
        $sql = $this->services->db->prepare(sprintf
        (
            'select distinct %s from %s_%s',
            $column,
            $prefix,
            $table
        ));

        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];

        foreach ($sql->fetchAll() as $row)
            $result[ ] = $row[ $column ];

        return $result;
    }
}