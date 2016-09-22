<?php

namespace TopicCards\DbBackend;


use TopicCards\iTopicMap;

class Search
{
    const EVENT_INDEX_PARAMS = 'search_index_params';

    protected $services;
    protected $elasticsearch = false;
    
    
    public function __construct(\TopicCards\iServices $services)
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
    
    
    public function search(\TopicCards\iTopicMap $topicmap, array $params)
    {
        return $this->callElasticSearch($topicmap, 'search', $params);
    }
    
    
    public function index(\TopicCards\iTopicMap $topicmap, array $params)
    {
        return $this->callElasticSearch($topicmap, 'index', $params);
    }
    
    
    public function get(\TopicCards\iTopicMap $topicmap, array $params)
    {
        return $this->callElasticSearch($topicmap, 'get', $params);
    }
    
    
    public function delete(\TopicCards\iTopicMap $topicmap, array $params)
    {
        return $this->callElasticSearch($topicmap, 'delete', $params);
    }


    public function callElasticSearch(\TopicCards\iTopicMap $topicmap, $method, array $params)
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
            // Delete on a non-indexed item returns a 404Exception, ignore that
            if ($e instanceof \Elasticsearch\Common\Exceptions\Missing404Exception)
            {
                $result = true;
            }
            else
            {
                trigger_error(sprintf("%s %s: %s", __METHOD__, $method, $e->getMessage()), E_USER_WARNING);
                $result = false;
            }
        }
        
        return $result;
    }
    
    
    public function getIndexParams(iTopicMap $topicmap, $index)
    {
        $params =
            [
                'index' => $index,
                'body' =>
                    [
                        'mappings' =>
                            [
                                'topic' =>
                                    [
                                        '_source' => [ 'enabled' => true ],
                                        'properties' =>
                                            [
                                                'has_name_type_id' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'has_occurrence_type_id' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'label' =>
                                                    [
                                                        'type' => 'string',
                                                        'fields' =>
                                                            [
                                                                'raw' =>
                                                                    [
                                                                        'type' => 'string',
                                                                        'index' => 'not_analyzed'
                                                                    ]
                                                            ]
                                                    ],
                                                'name' =>
                                                    [
                                                        'type' => 'string'
                                                    ],
                                                'occurrence' =>
                                                    [
                                                        'type' => 'string'
                                                    ],
                                                'subject' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'topic_type_id' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ]
                                            ]
                                    ],
                                'association' =>
                                    [
                                        '_source' => [ 'enabled' => true ],
                                        'properties' =>
                                            [
                                                'association_type_id' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'has_player_id' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'has_role_type_id' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ]
                                            ]
                                    ],
                                'history' =>
                                    [
                                        '_source' => [ 'enabled' => true ],
                                        'properties' =>
                                            [
                                                'dml' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'id' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'type' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'user_id' =>
                                                    [
                                                        'type' => 'string',
                                                        'index' => 'not_analyzed'
                                                    ],
                                                'when' =>
                                                    [
                                                        'type' => 'date'
                                                    ]
                                            ]
                                    ]
                            ]
                    ]
            ];

        $callback_result = [ ];

        $topicmap->trigger
        (
            self::EVENT_INDEX_PARAMS,
            [ 'index_params' => $params ],
            $callback_result
        );

        if (isset($callback_result[ 'index_params' ]) && is_array($callback_result[ 'index_params' ]))
            $params = $callback_result[ 'index_params' ];

        return $params;
    }


    public function recreateIndex(iTopicMap $topicmap, $index, array $params)
    {
        $this->init();

        if (strlen($index) === 0)
        {
            $index = $topicmap->getSearchIndex();
        }

        $elasticsearch = $this->getElasticSearchClient();

        if ($elasticsearch->indices()->exists([ 'index' => $index ]))
        {
            $elasticsearch->indices()->delete([ 'index' => $index ]);
        }

        $elasticsearch->indices()->create($params);

        return 1;
    }


    public function reindexAllTopics(iTopicMap $topicmap)
    {
        $limit = 0;
        $topic_ids = $topicmap->getTopicIds([ 'limit' => $limit ]);

        $topic = $topicmap->newTopic();
        $cnt = 0;

        foreach ($topic_ids as $topic_id)
        {
            $ok = $topic->load($topic_id);

            if ($ok >= 0)
                $ok = $topic->index();

            printf("#%d %s (%s)\n", ++$cnt, $topic->getId(), $ok);

            if (($limit > 0) && ($cnt >= $limit))
                break;
        }
        
        return $cnt;
    }


    public function reindexAllAssociations(iTopicMap $topicmap)
    {
        $limit = 0;
        $association_ids = $topicmap->getAssociationIds([ 'limit' => $limit ]);

        $association = $topicmap->newAssociation();
        $cnt = 0;

        foreach ($association_ids as $association_id)
        {
            $ok = $association->load($association_id);

            if ($ok >= 0)
                $ok = $association->index();

            if (($limit > 0) && ($cnt >= $limit))
                break;
        }
        
        return $cnt;
    }
}
