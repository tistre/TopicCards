<?php

namespace Xddb\Backends\Db;


trait NameDbAdapter
{
    public function selectAll(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
        $sql = $this->services->db->prepare(sprintf
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
            $row = $this->services->db_utils->stripColumnPrefix('name_', $row);
            $row[ 'scope' ] = $this->selectScopes([ 'name' => intval($row[ 'id' ]) ]);
            
            $result[ ] = $row;
        }
        
        return $result;
    }


    public function insertAll($topic_id, array $data)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        foreach ($data as $name_data)
        {
            $values = [ ];
        
            $name_data[ 'topic' ] = $topic_id;

            foreach ($name_data as $key => $value)
            {
                if ($key === 'scope')
                    continue;
                    
                $values[ ] =
                [
                    'column' => 'name_' . $key,
                    'value' => $value
                ];
            }
        
            $sql = $this->services->db_utils->prepareInsertSql($this->services->topicmap->getUrl() . '_name', $values);
        
            $ok = $sql->execute();
        
            if ($ok === false)
                return -1;
            
            $name_id = $this->services->db->lastInsertId();
            
            $ok = $this->insertScopes('name', $name_id, $name_data[ 'scope' ]);
            
            if ($ok < 0)
                return $ok;
        }
        
        return 1;
    }
}
