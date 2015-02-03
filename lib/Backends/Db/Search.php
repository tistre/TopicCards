<?php

namespace TopicBank\Backends\Db;


class Search
{
    const EVENT_INDEX_PARAMS = 'search_index_params';

    protected $services;
    protected $elasticsearch = false;
    
    
    public function __construct(\TopicBank\Interfaces\iServices $services)
    {
        $this->services = $services;
    }
    
    
    public function init()
    {
        if ($this->elasticsearch !== false)
            return 0;
        
        $this->elasticsearch = new \Elasticsearch\Client($this->services->getSearchParams());
        
        return 1;
    }
    
    
    public function getElasticSearchClient()
    {
        return $this->elasticsearch;
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
            $result = $this->elasticsearch->$method($params);
        }
        catch (\Exception $e)
        {
            trigger_error(sprintf("%s %s: %s", __METHOD__, $method, $e->getMessage()), E_USER_WARNING);
            $result = false;
        }
        
        return $result;
    }
}
