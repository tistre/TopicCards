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


    public function getTypeSubject()
    {
        return $this->getTopicMap()->getTopicSubject($this->type);
    }

    
    public function setTypeSubject($topic_subject)
    {
        $topic_id = $this->getTopicMap()->getTopicBySubject($topic_subject);
        
        if (strlen($topic_id) === 0)
            return -1;
            
        return $this->setType($topic_id);
    }


    public function hasType($topic_id)
    {
        return ($this->getType() === $topic_id);
    }
    
    
    public function hasTypeSubject($topic_subject)
    {
        return ($this->getTypeSubject() === $topic_subject);
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
