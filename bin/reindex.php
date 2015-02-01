<?php

require_once dirname(__DIR__) . '/include/config.php';

$services->search->init();

// Recreate index (XXX make this optional)

printf("Deleting index %s...\n", $topicmap->getSearchIndex());

$response = $services->search->getElasticSearchClient()->indices()->delete([ 'index' => $topicmap->getSearchIndex() ]);

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

printf("Creating index %s...\n", $topicmap->getSearchIndex());

$response = $services->search->getElasticSearchClient()->indices()->create($params);

print_r($response);

echo "Indexing topics...\n";

$topic = $topicmap->newTopic();

foreach ($topicmap->getTopicIds([ ]) as $topic_id)
{
    $ok = $topic->load($topic_id);
    
    if ($ok >= 0)
        $ok = $topic->index();
    
    printf("%s (%s)\n", $topic->getId(), $ok);
}

echo "Indexing associations...\n";

$association = $topicmap->newAssociation();

foreach ($topicmap->getAssociationIds([ ]) as $association_id)
{
    $ok = $association->load($association_id);
    
    if ($ok >= 0)
        $ok = $association->index();
    
    printf("%s (%s)\n", $association->getId(), $ok);
}

echo "Done.\n";
