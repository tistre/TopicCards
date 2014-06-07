<?php

namespace TopicBank\Backends\Db;


trait Typed
{
    use TypedDbAdapter;
    
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


    public function getAllTyped()
    {
        return
        [
            'type' => $this->getType()
        ];
    }


    public function setAllTyped(array $data)
    {
        $data = array_merge(
        [
            'type' => false
        ], $data);
        
        return $this->setType($data[ 'type' ]);
    }
}
