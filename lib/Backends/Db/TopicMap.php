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
        
        $topic->setTopicMap($this);
        
        return $topic;
    }
    
    
    public function getTopics(array $filters)
    {
        return $this->services->db->selectTopicIds($this, $filters);
    }
    
    
    public function newAssociation()
    {
        $association = new Association($this->services);
        
        $association->setTopicMap($this);
        
        return $association;
    }
    
    
    public function getAssociations(array $filters)
    {
        return $this->services->db->selectAssociationIds($this, $filters);
    }
}
