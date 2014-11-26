<?php

require_once dirname(__DIR__) . '/include/config.php';

$services->search_utils->init();

// Recreate index (XXX make this optional)

printf("Deleting index %s...\n", $topicmap->getSearchIndex());

$response = $services->search->indices()->delete([ 'index' => $topicmap->getSearchIndex() ]);

print_r($response);

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
                    'type_id' => 
                    [ 
                        'type' => 'string',
                        'index' => 'not_analyzed'
                    ]
                ]
            ]
        ]
    ]
];

printf("Creating index %s...\n", $topicmap->getSearchIndex());

$response = $services->search->indices()->create($params);

print_r($response);

echo "Indexing documents...\n";

$services->db_utils->connect();

$topic = $topicmap->newTopic();

foreach ($topicmap->getTopics([ ]) as $topic_id)
{
    $ok = $topic->load($topic_id);
    
    if ($ok >= 0)
        $ok = $topic->index();
    
    printf("%s (%s)\n", $topic->getId(), $ok);
}

echo "Done.\n";
