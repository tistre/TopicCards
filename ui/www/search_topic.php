<?php

require_once dirname(dirname(__DIR__)) . '/include/www_init.php';

/** @var \TopicCards\Interfaces\iTopicMap $topicmap */

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$fulltext_query = $_REQUEST[ 'name' ];
$type_query = $_REQUEST[ 'type' ];

$page_size = 10;
$page_num = 1;

if (isset($_REQUEST[ 'p' ]))
    $page_num = max($page_num, intval($_REQUEST[ 'p' ]));

$tpl[ 'page_num' ] = $page_num;

$query = 
[ 
    'size' => $page_size,
    'from' => ($page_size * ($page_num - 1)),
    // XXX add date sorting
    'sort' => (strlen($fulltext_query) > 0 ? '_score' : 'label.raw')
];

if (strlen($fulltext_query) > 0)
    $query[ 'query' ][ 'filtered' ][ 'query' ][ 'match' ][ '_all' ] = $fulltext_query;

if (strlen($type_query) > 0)
    $query[ 'query' ][ 'filtered' ][ 'filter' ][ 'term' ][ 'topic_type_id' ] = $type_query;
    
$response = [ ];
    
$response = $topicmap->getSearch()->search
(
    [
        'type' => 'topic',
        'body' => $query
    ]
);

$tpl[ 'results' ] = [ ];

foreach ($response[ 'hits' ][ 'hits' ] as $hit)
{
    $label = $hit[ '_source' ][ 'label' ];
    
    if (strlen($label) === 0)
        $label = $hit[ '_id' ];

    $types = [ ];
    
    foreach ($hit[ '_source' ][ 'topic_type_id' ] as $type_id)
        $types[ ] = $topicmap->getTopicLabel($type_id);

    $tpl[ 'results' ][ ] = 
    [
        'id' => $hit[ '_id' ],
        'label' => $label,
        'type' => implode(', ', $types)
    ];
}

$tpl[ 'total_hits' ] = $response[ 'hits' ][ 'total' ];

$last_page = intval(ceil($response[ 'hits' ][ 'total' ] / $page_size));

$tpl[ 'pages' ] = 
[
    'first' => 
    [
        'page_num' => 1,
        'label' => '<<'
    ],
    'previous' => 
    [
        'page_num' => max(1, ($page_num - 1)),
        'label' => '<'
    ],
    'next' =>
    [
        'page_num' => min($last_page, ($page_num + 1)),
        'label' => '>'
    ],
    'last' => 
    [
        'page_num' => $last_page,
        'label' => '>>'
    ]
];

include TOPICBANK_BASE_DIR . '/ui/templates/search_topic.tpl.php';
