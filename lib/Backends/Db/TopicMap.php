<?php

namespace TopicBank\Backends\Db;


class TopicMap extends Core implements \TopicBank\Interfaces\iTopicMap
{
    use Reified, TopicMapDbAdapter;
     
    protected $url;
    
    
    public function setUrl($url)
    {
        $this->url = $url;
        
        return 1;
    }
    
    
    public function getUrl()
    {
        return $this->url;
    }
    
    
    public function newTopic()
    {   
        $topic = new Topic($this->services);
        
        return $topic;
    }
    
    
    public function getTopics(array $filters)
    {
        return $this->selectTopics($filters);
    }
    
    
    public function newAssociation()
    {
        $association = new Association($this->services);
        
        return $association;
    }
    
    
    public function getAssociations(array $filters)
    {
        return $this->selectAssociations($filters);
    }


    public function getTopicTypes(array $filters)
    {
        return $this->selectTopicTypes($filters);
    }


    public function getNameTypes(array $filters)
    {
        return $this->selectNameTypes($filters);
    }


    public function getOccurrenceTypes(array $filters)
    {
        return $this->selectOccurrenceTypes($filters);
    }


    public function getOccurrenceDatatypes(array $filters)
    {
        return $this->selectOccurrenceDatatypes($filters);
    }


    public function getAssociationTypes(array $filters)
    {
        return $this->selectAssociationTypes($filters);
    }


    public function getRoleTypes(array $filters)
    {
        return $this->selectRoleTypes($filters);
    }
}
