<?php

namespace TopicBank\Backends\Db;


class Search
{
    protected $services;
    protected $es_client = false;
    
    
    public function __construct(\TopicBank\Interfaces\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function init()
    {
        if ($this->es_client !== false)
            return 0;
        
        $this->es_client = new \Elasticsearch\Client($this->services->getSearchParams());
        
        return 1;
    }
    
    
    public function getElasticSearchClient()
    {
        return $this->es_client;
    }
    
    
    public function search(\TopicBank\Interfaces\iTopicMap $topicmap, array $params)
    {
        return $this->callElasticSearch($topicmap, 'search', $params);
    }
    
    
    public function index(\TopicBank\Interfaces\iTopicMap $topicmap, array $params)
    {
        return $this->callElasticSearch($topicmap, 'index', $params);
    }
    
    
    public function get(\TopicBank\Interfaces\iTopicMap $topicmap, array $params)
    {
        return $this->callElasticSearch($topicmap, 'get', $params);
    }
    
    
    public function delete(\TopicBank\Interfaces\iTopicMap $topicmap, array $params)
    {
        return $this->callElasticSearch($topicmap, 'delete', $params);
    }


    public function callElasticSearch(\TopicBank\Interfaces\iTopicMap $topicmap, $method, array $params)
    {
        $ok = $this->init();
        
        if ($ok < 0)
            return false;
            
        if (! isset($params[ 'index' ]))
            $params[ 'index' ] = $topicmap->getSearchIndex();
        
        try
        {
            $result = $this->es_client->$method($params);
        }
        catch (\Exception $e)
        {
            trigger_error(sprintf("%s %s: %s", __METHOD__, $method, $e->getMessage()), E_USER_WARNING);
            $result = false;
        }
        
        return $result;
    }
}
