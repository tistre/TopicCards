<?php

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

require_once dirname(__DIR__) . '/include/config.php';

$getopt = new Getopt(
[
    new Option('l', 'limit', Getopt::REQUIRED_ARGUMENT)
]);

$getopt->parse();

$limit = 20;

if ($getopt[ 'limit' ])
    $limit = $getopt[ 'limit' ];
    
$qstring = $getopt->getOperand(0);

$query = 
[ 
    'size' => $limit,
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
