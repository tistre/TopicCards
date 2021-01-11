<?php

namespace TopicBank\Backends\Db;


trait Id
{
    protected $id = false;
    
    
    public function getId()
    {
        return $this->id;
    }
    
    
    public function setId($id)
    {
        $this->id = $id;
        return 1;
    }


    public function getAllId()
    {   
        return
        [
            'id' => $this->getId()
        ];
    }
    
        
    public function setAllId(array $data)
    {
        $data = array_merge(
        [
            'id' => false 
        ], $data);
        
        $this->setId($data[ 'id' ]);
        
        return 1;
    }
}
