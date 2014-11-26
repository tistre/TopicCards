<?php

require_once dirname(__DIR__) . '/include/config.php';

$page_size = 20;
$qstring = $argv[ 1 ];

$query = 
[ 
    'size' => $page_size,
    'query' => [ 'query_string' => [ 'query' => $qstring ] ]
];

$services->search_utils->init();

try
{
    $response = $services->search->search(array
    (
        'index' => $topicmap->getSearchIndex(),
        'type' => 'topic',
        'body' => $query
    ));
}
catch (\Exception $e)
{
    trigger_error(sprintf("%s: %s", __METHOD__, $e->getMessage()), E_USER_WARNING);
    exit;
}

foreach ($response[ 'hits' ][ 'hits' ] as $hit)
    echo $hit[ '_id' ] . "\n";
