<?php

namespace Xddb\Backends\Db;


trait Reified
{
    protected $reifier;
    
    
    public function getReifier()
    {
        return $this->reifier;
    }
    
    
    public function setReifier($topic_id)
    {
        $this->reifier = $topic_id;
        return 1;
    }


    public function setAllReified(array $data)
    {
        return $this->setReifier(isset($data[ 'reifier' ]) ? $data[ 'reifier' ] : false);
    }
}
