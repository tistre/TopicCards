<?php

namespace TopicBank\Backends\Db;

use \TopicBank\Interfaces\iTopic;


trait Reified
{
    protected $reifier;
    
    
    public function getReifierId()
    {
        return $this->reifier;
    }
    
    
    public function setReifierId($topic_id)
    {
        $this->reifier = $topic_id;
        return 1;
    }


    public function getAllReified()
    {
        return
        [
            'reifier' => $this->getReifierId()
        ];
    }


    public function setAllReified(array $data)
    {
        $data = array_merge(
        [
            'reifier' => false
        ], $data);
        
        return $this->setReifierId($data[ 'reifier' ]);
    }
    
    
    public function newReifierTopic()
    {
        $reifier_id = $this->topicmap->createId();
        
        $reifier_topic = $this->topicmap->newTopic();
        $reifier_topic->setId($reifier_id);
        
        if ($this instanceof \TopicBank\Interfaces\iName)
        {
            $is_reifier = iTopic::REIFIES_NAME;
        }
        elseif ($this instanceof \TopicBank\Interfaces\iOccurrence)
        {
            $is_reifier = iTopic::REIFIES_OCCURRENCE;
        }
        elseif ($this instanceof \TopicBank\Interfaces\iAssociation)
        {
            $is_reifier = iTopic::REIFIES_ASSOCIATION;
        }
        elseif ($this instanceof \TopicBank\Interfaces\iRole)
        {
            $is_reifier = iTopic::REIFIES_ROLE;
        }
        
        $reifier_topic->setIsReifier($is_reifier);
        
        $this->setReifierId($reifier_id);
        
        return $reifier_topic;
    }
}
