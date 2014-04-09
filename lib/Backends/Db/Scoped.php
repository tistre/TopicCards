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


    public function setAllScoped(array $data)
    {
        return $this->setScope(isset($data[ 'scope' ]) ? $data[ 'scope' ] : [ ]);
    }
}
