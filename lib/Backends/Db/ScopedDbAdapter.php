<?php

namespace Xddb\Backends\Db;


trait ScopedDbAdapter
{
    protected function selectScopes(array $filters)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        $where = [ ];
        
        foreach ($filters as $key => $value)
        {
            $where[ ] = 
            [
                'column' => 'scope_' . $key,
                'value' => $value
            ];
        }
        
        $prefix = $this->services->topicmap->getUrl();

        $sql = $this->services->db_utils->prepareSelectSql($prefix . '_scope', 'scope_scope', $where);
        
        $ok = $sql->execute();
        
        if ($ok === false)
            return -1;

        $rows = $sql->fetchAll();
        
        return array_column($rows, 'scope_scope');
    }


    protected function insertScopes($obj_type, $obj_id, array $scope)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;
        
        foreach ($scope as $topic_id)
        {
            $values = [ ];

            $values[ ] =
            [
                'column' => 'scope_' . $obj_type,
                'value' => $obj_id,
                'datatype' => ($obj_type === 'association' ? \PDO::PARAM_STR : \PDO::PARAM_INT)
            ];
        
            $values[ ] =
            [
                'column' => 'scope_scope',
                'value' => $topic_id
            ];
        
            $sql = $this->services->db_utils->prepareInsertSql($this->services->topicmap->getUrl() . '_scope', $values);
        
            $ok = $sql->execute();
        
            if ($ok === false)
                return -1;
        }
        
        return 1;
    }
    
    
    protected function updateScopes($obj_type, $obj_id, array $scope)
    {
        $ok = $this->services->db_utils->connect();
        
        if ($ok < 0)
            return $ok;

        $sql = $this->services->db_utils->prepareDeleteSql
        (
            $this->services->topicmap->getUrl() . '_scope', 
            [ [
                'column' => 'scope_' . $obj_type,
                'value' => $obj_id,
                'datatype' => ($obj_type === 'association' ? \PDO::PARAM_STR : \PDO::PARAM_INT)
            ] ]
        );
    
        $ok = $sql->execute();
    
        if ($ok === false)
            return -1;
        
        return $this->insertScopes($obj_type, $obj_id, $scope);
    }
}
