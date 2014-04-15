<?php

namespace Xddb\Backends\Db;


class TopicMap extends Core implements \Xddb\Interfaces\iTopicMap
{
    use Reified;
     
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
        return $this->services->db_utils->selectTopicIds($filters);
    }
    
    
    public function newAssociation()
    {
        $association = new Association($this->services);
        
        return $association;
    }
    
    
    public function getAssociations(array $filters)
    {
        return $this->services->db_utils->selectAssociationIds($filters);
    }
}
