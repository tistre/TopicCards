<?php

use TopicBank\Interfaces\iTopic;

require_once dirname(dirname(__DIR__)) . '/include/config.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'label' ] = $topicmap->getTopicLabel($topicmap->getReifier());


$fulltext_query = '';

if (isset($_REQUEST[ 'q' ]))
    $fulltext_query = $_REQUEST[ 'q' ];

$type_query = '';

if (isset($_REQUEST[ 'type' ]))
    $type_query = $_REQUEST[ 'type' ];

$page_size = 50;
$page_num = 1;

if (isset($_REQUEST[ 'p' ]))
    $page_num = max($page_num, intval($_REQUEST[ 'p' ]));

$tpl[ 'page_num' ] = $page_num;

$query = 
[ 
    'size' => $page_size,
    'from' => ($page_size * ($page_num - 1)),
    'sort' => 'label.raw'
];

if (strlen($fulltext_query) > 0)
    $query[ 'query' ][ 'filtered' ][ 'query' ][ 'match' ][ 'name' ] = $fulltext_query;

if (strlen($type_query) > 0)
    $query[ 'query' ][ 'filtered' ][ 'filter' ][ 'term' ][ 'type_id' ] = $type_query;
    
$response = [ ];
    
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
}

$tpl[ 'fulltext_query' ] = $fulltext_query;

$tpl[ 'topics' ] = [ ];
$type_labels = [ ];

foreach ($response[ 'hits' ][ 'hits' ] as $hit)
{
    $types = [ ];
    
    foreach ($hit[ '_source' ][ 'type_id' ] as $type)
    {
        if (! isset($type_labels[ $type ]))
            $type_labels[ $type ] = $topicmap->getTopicLabel($type);
        
        $types[ ] = $type_labels[ $type ];
    }

    $label = $hit[ '_source' ][ 'label' ];
    
    if (strlen($label) === 0)
        $label = $hit[ '_id' ];

    $tpl[ 'topics' ][ ] = 
    [
        'id' => $hit[ '_id' ],
        'label' => $label,
        'type' => implode(', ', $types),
        'url' => sprintf('%stopic/%s', TOPICBANK_BASE_URL, $hit[ '_id' ])
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

$tpl[ 'topic_types' ] = [ ];

// XXX slow
foreach ($topicmap->getTopicTypes([ 'get_mode' => 'all' ]) as $id)
{
    $tpl[ 'topic_types' ][ ] = 
    [
        'id' => $id,
        'label' => $topicmap->getTopicLabel($id),
        'selected' => ($id === $type_query)
    ];
}

include TOPICBANK_BASE_DIR . '/ui/templates/topics.tpl.php';
