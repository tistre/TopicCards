<?php

namespace Xddb\Backends\Db;


trait Typed
{
    protected $type = false;
    
    
    public function getType()
    {
        return $this->type;
    }
    
    
    public function setType($topic_id)
    {
        $this->type = $topic_id;
        return 1;
    }


    public function setAllTyped(array $data)
    {
        return $this->setType(isset($data[ 'type' ]) ? $data[ 'type' ] : false);
    }
}
