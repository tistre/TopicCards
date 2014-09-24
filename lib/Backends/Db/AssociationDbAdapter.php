<?php

namespace TopicBank\Backends\Db;


trait AssociationDbAdapter
{
    public function selectAll(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $prefix = $this->services->topicmap->getUrl();

        if (isset($filters[ 'id' ]))
        {
            $where = 'association_id = :association_id';
        }
        elseif (isset($filters[ 'reifier' ]))
        {
            $where = 'association_reifier = :reifier_id';
        }
        
        $sql = $this->services->db->prepare(sprintf
        (
            'select * from %s_association'
            . ' where ' . $where, 
            $prefix
        ));

        if (isset($filters[ 'id' ]))
        {
            $sql->bindValue(':association_id', $filters[ 'id' ], \PDO::PARAM_STR);
        }
        elseif (isset($filters[ 'reifier' ]))
        {
            $sql->bindValue(':reifier_id', $filters[ 'reifier' ], \PDO::PARAM_STR);
        }
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $result = [ ];
        
        $role = new Role($this->services);
        
        foreach ($sql->fetchAll() as $row)
        {
            $row = $this->services->db_utils->stripColumnPrefix('association_', $row);

            $row[ 'scope' ] = $this->selectScopes([ 'association' => $row[ 'id' ] ]);
            
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
        
        $this->services->db_utils->beginTransaction();
        
        $now = date('c');        
        $data[ 'created' ] = $data[ 'updated' ] = $now;
        
        $data[ 'version' ] = 1;
        
        $values = [ ];
        
        foreach ($data as $key => $value)
        {
            $ignore = [ 'roles', 'scope' ];
            
            if (in_array($key, $ignore))
                continue;
            
            if (($key === 'created') || ($key === 'updated'))
                $value = $this->services->db_utils->datetimeToDb($value);
            
            $datatype = \PDO::PARAM_STR;
            
            if ($key === 'version')
                $datatype = \PDO::PARAM_INT;
                
            $values[ ] =
            [
                'column' => 'association_' . $key,
                'value' => $value,
                'datatype' => $datatype
            ];
        }
        
        $sql = $this->services->db_utils->prepareInsertSql($this->services->topicmap->getUrl() . '_association', $values);
        
        $ok = $sql->execute();
        
        $ok = ($ok === false ? -1 : 1);
        
        if ($ok >= 0)
            $ok = $this->insertScopes('association', $data[ 'id' ], $data[ 'scope' ]);

        if ($ok >= 0)
        {
            $role = new Role($this->services);
            $ok = $role->insertAll($data[ 'id' ], $data[ 'roles' ]);
        }

        if ($ok < 0)
        {
            $this->services->db_utils->rollBack();
            return $ok;
        }

        $this->services->db_utils->commit();

        return $ok;
    }
    
    
    public function updateAll(array $data)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $this->services->db_utils->beginTransaction();
        
        $previous_version = $data[ 'version' ];
        
        $data[ 'updated' ] = date('c');        
        $data[ 'version' ]++;
        
        $values = [ ];
        
        foreach ($data as $key => $value)
        {
            $ignore = [ 'id', 'created', 'roles', 'scope' ];
            
            if (in_array($key, $ignore))
                continue;
            
            if (($key === 'created') || ($key === 'updated'))
                $value = $this->services->db_utils->datetimeToDb($value);
            
            $datatype = \PDO::PARAM_STR;
            
            if ($key === 'version')
                $datatype = \PDO::PARAM_INT;
                
            $values[ ] =
            [
                'column' => 'association_' . $key,
                'value' => $value,
                'datatype' => $datatype
            ];
        }
        
        $sql = $this->services->db_utils->prepareUpdateSql
        (
            $this->services->topicmap->getUrl() . '_association', 
            $values,
            [
                [
                    'column' => 'association_id',
                    'value' => $data[ 'id' ]
                ],
                [
                    'column' => 'association_version',
                    'value' => $previous_version,
                    'datatype' => \PDO::PARAM_INT
                ]
            ]
        );

        $ok = $sql->execute();
        
        $ok = ($ok === false ? -1 : 1);
        
        if (($ok >= 0) && ($sql->rowCount() !== 1))
            $ok = -2;

        if ($ok >= 0)
            $ok = $this->updateScopes('association', $data[ 'id' ], $data[ 'scope' ]);
        
        if ($ok >= 0)
        {
            $role = new Role($this->services);
            $ok = $role->updateAll($data[ 'id' ], $data[ 'roles' ]);
        }

        if ($ok < 0)
        {
            $this->services->db_utils->rollBack();
            return $ok;
        }

        $this->services->db_utils->commit();

        return $ok;
    }


    public function deleteById($id, $version)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;

        $prefix = $this->services->topicmap->getUrl();

        $sql = $this->services->db_utils->prepareDeleteSql
        (
            $prefix . '_association', 
            [ 
                [ 'column' => 'association_id', 'value' => $id ],
                [ 'column' => 'association_version', 'value' => $version ]
            ]
        );
    
        $ok = $sql->execute();
    
        if ($ok === false)
            return -1;
        
        return 1;
    }
}
