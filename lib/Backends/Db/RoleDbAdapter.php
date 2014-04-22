<?php

namespace Xddb\Backends\Db;


trait RoleDbAdapter
{
    public function selectAll(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();
        
        $sql = $this->services->db->prepare(sprintf
        (
            'select * from %s_role'
            . ' where role_association = :association_id', 
            $prefix
        ));

        $sql->bindValue(':association_id', $filters[ 'association' ], \PDO::PARAM_STR);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        foreach ($sql->fetchAll() as $row)
        {
            $row = $this->services->db_utils->stripColumnPrefix('role_', $row);
            $result[ ] = $row;
        }

        return $result;
    }


    public function insertAll($association_id, array $data)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        foreach ($data as $name_data)
        {
            $values = [ ];
        
            $name_data[ 'association' ] = $association_id;

            foreach ($name_data as $key => $value)
            {
                $values[ ] =
                [
                    'column' => 'role_' . $key,
                    'value' => $value
                ];
            }
        
            $sql = $this->services->db_utils->prepareInsertSql($this->services->topicmap->getUrl() . '_role', $values);
        
            $ok = $sql->execute();
        
            if ($ok === false)
                return -1;
        }
        
        return 1;
    }
}
