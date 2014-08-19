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
    
    
    public function createId()
    {
        // http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/2040279#2040279

        return sprintf
        ( 
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
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
    
    
    public function getTopicLabel($id)
    {
        if (strlen($id) === 0)
            return false;
            
        $topic = $this->newTopic();
        
        $ok = $topic->load($id);
        
        if ($ok < 0)
            return false;
        
        return $topic->getLabel();
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


    public function getRolePlayers(array $filters)
    {
        return $this->selectRolePlayers($filters);
    }
}
