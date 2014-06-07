<?php

namespace TopicBank\Backends\Db;


trait Persistent
{
    protected $id = false;
    protected $created = false;
    protected $updated = false;
    protected $version = 0;
    protected $loaded = false;
    
    
    public function getId()
    {
        return $this->id;
    }
    
    
    public function setId($id)
    {
        $this->id = $id;
        return 1;
    }


    public function getCreated()
    {
        return $this->created;
    }
    
    
    public function setCreated($date)
    {
        $this->created = $date;
        return 1;
    }
    
    
    public function getUpdated()
    {
        return $this->updated;
    }
    
    
    public function setUpdated($date)
    {
        $this->updated = $date;
        return 1;
    }
    
    
    public function getVersion()
    {
        return $this->version;
    }
    
    
    public function setVersion($version)
    {
        $this->version = intval($version);
        return 1;
    }
    
    
    public function getAllPersistent()
    {   
        return
        [
            'id' => $this->getId(), 
            'created' => $this->getCreated(), 
            'updated' => $this->getUpdated(), 
            'version' => $this->getVersion()
        ];
    }
    
        
    public function setAllPersistent(array $data)
    {
        $data = array_merge(
        [
            'id' => false, 
            'created' => false,
            'updated' => false,
            'version' => 0
        ], $data);
        
        $this->setId($data[ 'id' ]);
        $this->setCreated($data[ 'created' ]);
        $this->setUpdated($data[ 'updated' ]);
        $this->setVersion($data[ 'version' ]);
        
        return 1;
    }
    
    
    public function load($id)
    {
        return -1;
    }
    
    
    public function isLoaded()
    {
        return $this->loaded;
    }
    
    
    public function save()
    {
        return -1;
    }
    
    
    public function delete()
    {
        return -1;
    }
}
