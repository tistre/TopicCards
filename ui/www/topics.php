<?php

use TopicBank\Interfaces\iTopic;

require_once dirname(dirname(__DIR__)) . '/include/www_init.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'label' ] = $topicmap->getTopicLabel($topicmap->getReifierId());


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
    // XXX add date sorting
    'sort' => (strlen($fulltext_query) > 0 ? '_score' : 'label.raw')
];

if (strlen($fulltext_query) > 0)
    $query[ 'query' ][ 'filtered' ][ 'query' ][ 'match' ][ '_all' ] = $fulltext_query;

if (strlen($type_query) > 0)
{
    $query[ 'query' ][ 'filtered' ][ 'filter' ][ 'term' ][ 'topic_type_id' ] = $type_query;
}
else
{
    $query[ 'facets' ][ 'types' ] = [ 'terms' => [ 'field' => 'topic_type_id' ] ];
}
        
$response = [ ];
    
$response = $services->search->search($topicmap,
[
    'type' => 'topic',
    'body' => $query
]);

$tpl[ 'fulltext_query' ] = $fulltext_query;

$tpl[ 'topics' ] = [ ];
$type_labels = [ ];

foreach ($response[ 'hits' ][ 'hits' ] as $hit)
{
    $types = [ ];
    
    foreach ($hit[ '_source' ][ 'topic_type_id' ] as $type_id)
    {
        if (! isset($type_labels[ $type_id ]))
            $type_labels[ $type_id ] = $topicmap->getTopicLabel($type_id);
        
        $types[ ] = $type_labels[ $type_id ];
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
foreach ($topicmap->getTopicTypeIds([ 'get_mode' => 'all' ]) as $id)
{
    $tpl[ 'topic_types' ][ ] = 
    [
        'id' => $id,
        'label' => $topicmap->getTopicLabel($id),
        'selected' => ($id === $type_query)
    ];
}

TopicBank\Utils\StringUtils::usortByKey($tpl[ 'topic_types' ], 'label');

$tpl[ 'type_facets' ] = [ ];

if (isset($response[ 'facets' ][ 'types' ]))
{
    foreach ($response[ 'facets' ][ 'types' ][ 'terms' ] as $facet_term)
    {
        $tpl[ 'type_facets' ][ ] =
        [
            'label' => $topicmap->getTopicLabel($facet_term[ 'term' ]),
            'count' => $facet_term[ 'count' ],
            'search_url' => sprintf
            (
                '%stopics?q=%s&type=%s', 
                TOPICBANK_BASE_URL,
                urlencode($fulltext_query),
                urlencode($facet_term[ 'term' ])
            )
        ];
    }
}

include TOPICBANK_BASE_DIR . '/ui/templates/topics.tpl.php';
