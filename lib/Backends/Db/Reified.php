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


    public function getAllReified()
    {
        return
        [
            'reifier' => $this->getReifier()
        ];
    }


    public function setAllReified(array $data)
    {
        $data = array_merge(
        [
            'reifier' => false
        ], $data);
        
        return $this->setReifier($data[ 'reifier' ]);
    }
}
