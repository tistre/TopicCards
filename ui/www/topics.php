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

$query = [ 'match' => [ ] ];

if (strlen($fulltext_query) > 0)
    $query[ 'match' ][ 'name' ] = $fulltext_query;

if (strlen($type_query) > 0)
    $query[ 'match' ][ 'type' ] = $type_query;

$topic_ids = [ ];
    
$services->search_utils->init();

try
{
    $response = $services->search->search(array
    (
        'index' => $topicmap->getSearchIndex(),
        'type' => 'topic',
        'body' => [ 'query' => $query ]
    ));

    foreach ($response[ 'hits' ][ 'hits' ] as $hit)
        $topic_ids[ ] = $hit[ '_id' ];
}
catch (\Exception $e)
{
    trigger_error(sprintf("%s: %s", __METHOD__, $e->getMessage()), E_USER_WARNING);
}

$tpl[ 'fulltext_query' ] = $fulltext_query;

$tpl[ 'topics' ] = [ ];

foreach ($topic_ids as $id)
{
    $topic = $topicmap->newTopic();
    $topic->load($id);
    
    $types = [ ];
    
    foreach ($topic->getTypes() as $type)
        $types[ ] = $topicmap->getTopicLabel($type);

    $label = $topic->getLabel();
    
    if (strlen($label) === 0)
        $label = $topic->getId();

    $tpl[ 'topics' ][ ] = 
    [
        'id' => $id,
        'label' => $label,
        'type' => implode(', ', $types),
        'url' => sprintf('%stopic/%s', TOPICBANK_BASE_URL, $id)
    ];
}

TopicBank\Utils\StringUtils::usortByKey($tpl[ 'topics' ], 'label');

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
