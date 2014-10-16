<?php

namespace TopicBank\Backends\Db;

use \TopicBank\Interfaces\iTopic;


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
    
    
    public function newReifierTopic()
    {
        $reifier_id = $this->services->getTopicMap()->createId();
        
        $reifier_topic = $this->services->getTopicMap()->newTopic();
        $reifier_topic->setId($reifier_id);
        
        if ($this instanceof \TopicBank\Interfaces\iName)
        {
            $is_reifier = iTopic::REIFIES_NAME;
        }
        elseif ($this instanceof \TopicBank\Interfaces\iName)
        {
            $is_reifier = iTopic::REIFIES_OCCURRENCE;
        }
        elseif ($this instanceof \TopicBank\Interfaces\iName)
        {
            $is_reifier = iTopic::REIFIES_ASSOCIATION;
        }
        elseif ($this instanceof \TopicBank\Interfaces\iName)
        {
            $is_reifier = iTopic::REIFIES_ROLE;
        }
        
        $reifier_topic->setIsReifier($is_reifier);
        
        $this->setReifier($reifier_id);
        
        return $reifier_topic;
    }
}
