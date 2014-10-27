<?php

use TopicBank\Interfaces\iTopic;

require_once dirname(dirname(__DIR__)) . '/include/config.php';

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;
$tpl[ 'topicbank_static_base_url' ] = TOPICBANK_STATIC_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'display_name' ] = $topicmap->getTopicLabel($topicmap->getReifier());


$fulltext_query = '';

if (isset($_REQUEST[ 'q' ]))
    $fulltext_query = $_REQUEST[ 'q' ];
    
$filter = [ 'name_like' => '', 'type' => '' ];

if (strlen($fulltext_query) > 0)
    $filter[ 'name_like' ] = '%' . $fulltext_query . '%';

if (isset($_REQUEST[ 'type' ]))
    $filter[ 'type' ] = $_REQUEST[ 'type' ];
    
$topic_ids = $topicmap->getTopics($filter);


$tpl[ 'fulltext_query' ] = $fulltext_query;

$tpl[ 'topics' ] = [ ];

foreach ($topic_ids as $id)
{
    $topic = $topicmap->newTopic();
    $topic->load($id);
    
    $types = [ ];
    
    foreach ($topic->getTypes() as $type)
        $types[ ] = $topicmap->getTopicLabel($type);

    $tpl[ 'topics' ][ ] = 
    [
        'id' => $id,
        'label' => $topicmap->getTopicLabel($id),
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
        'selected' => ($id === $filter[ 'type' ])
    ];
}


include TOPICBANK_BASE_DIR . '/ui/templates/topics.tpl.php';
