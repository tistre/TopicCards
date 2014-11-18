<?php

namespace TopicBank\Backends\Db;


class TopicMap implements \TopicBank\Interfaces\iTopicMap
{
    use TopicMapDbAdapter;
     
    protected $url;
    protected $services;
    protected $db_table_prefix;
    protected $search_index;
    

    public function __construct(\TopicBank\Interfaces\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function getServices()
    {
        return $this->services;
    }
        
    
    public function setUrl($url)
    {
        $this->url = $url;
        
        return 1;
    }
    
    
    public function getUrl()
    {
        return $this->url;
    }
    

    public function setDbTablePrefix($prefix)
    {
        $this->db_table_prefix = $prefix;
        
        return 1;
    }
    
    
    public function getDbTablePrefix()
    {
        return $this->db_table_prefix;
    }
    
    
    public function setSearchIndex($index)
    {
        $this->search_index = $index;
        
        return 1;
    }
    
    
    public function getSearchIndex()
    {
        return $this->search_index;
    }
    
    
    public function getReifier()
    {
        return $this->getTopicBySubjectIdentifier($this->getUrl());
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
        $topic = new Topic($this->services, $this);
        
        return $topic;
    }
    
    
    public function getTopics(array $filters)
    {
        return $this->selectTopics($filters);
    }
    

    public function getTopicBySubjectIdentifier($uri)
    {
        return $this->selectTopicBySubjectIdentifier($uri);
    }
    
    
    public function getTopicSubjectIdentifier($topic_id)
    {
        if (strlen($topic_id) === 0)
            return false;
            
        return $this->selectTopicSubjectIdentifier($topic_id);
    }
    
    
    public function getTopicRef($topic_id)
    {
        if (strlen($topic_id) === 0)
            return false;
        
        $result = $this->getTopicSubjectIdentifier($topic_id);
        
        if (strlen($result) === 0)
            $result = '#' . $topic_id;
            
        return $result;
    }
    
    
    public function getTopicLabel($topic_id)
    {
        if (strlen($topic_id) === 0)
            return false;
            
        $topic = $this->newTopic();
        
        $ok = $topic->load($topic_id);
        
        if ($ok < 0)
            return false;
        
        return $topic->getLabel();
    }
    
    
    public function newAssociation()
    {
        $association = new Association($this->services, $this);
        
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


    public function getNameScopes(array $filters)
    {
        return $this->selectNameScopes($filters);
    }


    public function getOccurrenceTypes(array $filters)
    {
        return $this->selectOccurrenceTypes($filters);
    }


    public function getOccurrenceDatatypes(array $filters)
    {
        return $this->selectOccurrenceDatatypes($filters);
    }


    public function getOccurrenceScopes(array $filters)
    {
        return $this->selectOccurrenceScopes($filters);
    }


    public function getAssociationTypes(array $filters)
    {
        return $this->selectAssociationTypes($filters);
    }


    public function getAssociationScopes(array $filters)
    {
        return $this->selectAssociationScopes($filters);
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
