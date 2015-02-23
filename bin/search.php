<?php

use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

require_once dirname(__DIR__) . '/include/init.php';

$getopt = new Getopt(
[
    new Option('l', 'limit', Getopt::REQUIRED_ARGUMENT),
    new Option(null, 'config', Getopt::REQUIRED_ARGUMENT),
    new Option('h', 'help')
]);

$getopt->parse();

if ($getopt[ 'help' ])
{
    $getopt->setBanner("\nTopicBank topic search, using Elasticsearch\n\n");
    
    echo $getopt->getHelpText();
    exit;
}

$limit = 20;

if ($getopt[ 'limit' ])
    $limit = $getopt[ 'limit' ];
    
$qstring = $getopt->getOperand(0);

$query = 
[ 
    'size' => $limit,
    'query' => [ 'query_string' => [ 'query' => $qstring ] ]
];

$response = $services->search->search($topicmap,
[
    'type' => 'topic',
    'body' => $query
]);

if ($response === false)
    exit;

foreach ($response[ 'hits' ][ 'hits' ] as $hit)
    echo $hit[ '_id' ] . "\n";
