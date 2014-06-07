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
}
