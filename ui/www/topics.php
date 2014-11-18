<?php

use TopicBank\Interfaces\iTopic;

require_once dirname(dirname(__DIR__)) . '/include/config.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;
$tpl[ 'topicbank_static_base_url' ] = TOPICBANK_STATIC_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'label' ] = $topicmap->getTopicLabel($topicmap->getReifier());


$fulltext_query = '';

if (isset($_REQUEST[ 'q' ]))
    $fulltext_query = $_REQUEST[ 'q' ];

$type_query = '';

if (isset($_REQUEST[ 'type' ]))
    $type_query = $_REQUEST[ 'type' ];

$query = 
[ 
    'size' => 20,
    // XXX must set up label.raw for sorting to work correctly, see
    // http://www.elasticsearch.org/guide/en/elasticsearch/guide/current/multi-fields.html
    'sort' => 'label'
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

foreach ($response[ 'hits' ][ 'hits' ] as $hit)
{
    $types = [ ];
    
    foreach ($hit[ '_source' ][ 'type_id' ] as $type)
        $types[ ] = $topicmap->getTopicLabel($type);

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

$tpl[ 'topic_types' ] = [ ];

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
