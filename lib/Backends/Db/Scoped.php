<?php

namespace Xddb\Backends\Db;


trait Scoped
{
    protected $scope = [ ];
    
    
    public function getScope()
    {
        return $this->scope;
    }
    
    
    public function setScope(array $topic_ids)
    {
        $this->scope = $topic_ids;
        return 1;
    }


    public function getAllScoped()
    {
        return
        [
            'scope' => $this->getScope()
        ];
    }


    public function setAllScoped(array $data)
    {
        $data = array_merge(
        [
            'scope' => [ ]
        ], $data);
        
        return $this->setScope($data[ 'scope' ]);
    }
}
