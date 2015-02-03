<?php

require_once dirname(__DIR__) . '/include/config.php';

$services->search->init();

$elasticsearch = $services->search->getElasticSearchClient();

// Recreate index (XXX make this optional)

if ($elasticsearch->indices()->exists([ 'index' => $topicmap->getSearchIndex() ]))
{
    printf("Deleting index %s...\n", $topicmap->getSearchIndex());

    $response = $elasticsearch->indices()->delete([ 'index' => $topicmap->getSearchIndex() ]);

    print_r($response);
}

$params =
[
    'index' => $topicmap->getSearchIndex(),
    'body' => 
    [
        'mappings' =>
        [
            'topic' =>
            [
                '_source' => [ 'enabled' => true ],
                'properties' => 
                [
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
                    'has_name_type' => 
                    [ 
                        'type' => 'string',
                        'index' => 'not_analyzed'
                    ],
                    'topic_type' => 
                    [ 
                        'type' => 'string',
                        'index' => 'not_analyzed'
                    ],
                    'subject' => 
                    [ 
                        'type' => 'string',
                        'index' => 'not_analyzed'
                    ],
                    'occurrence' => 
                    [ 
                        'type' => 'string'
                    ],
                    'has_occurrence_type' => 
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
                    'association_type' => 
                    [ 
                        'type' => 'string',
                        'index' => 'not_analyzed'
                    ],
                    'has_role_type' => 
                    [ 
                        'type' => 'string',
                        'index' => 'not_analyzed'
                    ],
                    'has_player_id' => 
                    [ 
                        'type' => 'string',
                        'index' => 'not_analyzed'
                    ]
                ]
            ]
        ]
    ]
];

$callback_result = [ ];

$topicmap->trigger
(
    \TopicBank\Backends\Db\Search::EVENT_INDEX_PARAMS, 
    [ 'index_params' => $params ],
    $callback_result
);

if (isset($callback_result[ 'index_params' ]) && is_array($callback_result[ 'index_params' ]))
    $params = $callback_result[ 'index_params' ];

printf("Creating index %s...\n", $topicmap->getSearchIndex());

$response = $elasticsearch->indices()->create($params);

print_r($response);

$limit = 0;

echo "Indexing topics...\n";

$topic = $topicmap->newTopic();
$cnt = 0;
$topic_start_time = microtime(true);

foreach ($topicmap->getTopicIds([ ]) as $topic_id)
{
    $ok = $topic->load($topic_id);
    
    if ($ok >= 0)
        $ok = $topic->index();
    
    printf("#%d %s (%s)\n", ++$cnt, $topic->getId(), $ok);
    
    if (($limit > 0) && ($cnt >= $limit))
        break;
}

$total_time = (microtime(true) - $topic_start_time);

if ($cnt === 0)
{
    echo "No topics indexed.\n";
}
else
{
    $topic_summary = sprintf
    (
        "%d topics indexed in %.1f s (%.3f s per topic).\n",
        $cnt,
        $total_time,
        ($total_time / $cnt)
    );
}

echo "Indexing associations...\n";

$association = $topicmap->newAssociation();
$cnt = 0;
$association_start_time = microtime(true);

foreach ($topicmap->getAssociationIds([ ]) as $association_id)
{
    $ok = $association->load($association_id);
    
    if ($ok >= 0)
        $ok = $association->index();
    
    printf("#%d %s (%s)\n", ++$cnt, $association->getId(), $ok);
    
    if (($limit > 0) && ($cnt >= $limit))
        break;
}

$total_time = (microtime(true) - $association_start_time);

if ($cnt === 0)
{
    $association_summary = "No associations indexed.\n";
}
else
{
    $association_summary = sprintf
    (
        "%d associations indexed in %.1f s (%.3f s per association).\n",
        $cnt,
        $total_time,
        ($total_time / $cnt)
    );
}

echo "Done.\n";

echo $topic_summary . $association_summary;

printf("Total time: %d s\n", (microtime(true) - $topic_start_time));
