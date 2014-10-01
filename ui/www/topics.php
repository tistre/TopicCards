<?php

use TopicBank\Interfaces\iTopic;

define('TOPICBANK_BASE_DIR', dirname(dirname(__DIR__)));
define('TOPICBANK_BASE_URL', '/topicbank/');
define('TOPICBANK_STATIC_BASE_URL', '/topicbank_static/');

require_once TOPICBANK_BASE_DIR . '/include/init.php';
require_once TOPICBANK_BASE_DIR . '/include/config.php';

$services = new \TopicBank\Backends\Db\Services();
$services->setDbParams($db_params);

$system = new \TopicBank\Backends\Db\TopicMapSystem($services);

$services->topicmap = $system->newTopicMap();
$services->topicmap->setUrl('xddb');

$tpl = [ ];

$tpl[ 'topicbank_base_url' ] = TOPICBANK_BASE_URL;
$tpl[ 'topicbank_static_base_url' ] = TOPICBANK_STATIC_BASE_URL;

$tpl[ 'topicmap' ] = [ ];
$tpl[ 'topicmap' ][ 'display_name' ] = 'My first topic map';


$fulltext_query = '';

if (isset($_REQUEST[ 'q' ]))
    $fulltext_query = $_REQUEST[ 'q' ];
    
$filter = [ 'name_like' => '', 'type' => '' ];

if (strlen($fulltext_query) > 0)
    $filter[ 'name_like' ] = '%' . $fulltext_query . '%';

if (isset($_REQUEST[ 'type' ]))
    $filter[ 'type' ] = $_REQUEST[ 'type' ];
    
$topic_ids = $services->topicmap->getTopics($filter);


$tpl[ 'fulltext_query' ] = $fulltext_query;

$tpl[ 'topics' ] = [ ];

foreach ($topic_ids as $id)
{
    $topic = $services->topicmap->newTopic();
    $topic->load($id);
    
    $types = [ ];
    
    foreach ($topic->getTypes() as $type)
        $types[ ] = $services->topicmap->getTopicLabel($type);

    $tpl[ 'topics' ][ ] = 
    [
        'id' => $id,
        'label' => $services->topicmap->getTopicLabel($id),
        'type' => implode(', ', $types),
        'url' => sprintf('%stopic/%s', TOPICBANK_BASE_URL, $id)
    ];
}

TopicBank\Utils\StringUtils::usortByKey($tpl[ 'topics' ], 'label');

$tpl[ 'topic_types' ] = [ ];

foreach ($services->topicmap->getTopicTypes([ 'get_mode' => 'all' ]) as $id)
{
    $tpl[ 'topic_types' ][ ] = 
    [
        'id' => $id,
        'label' => $services->topicmap->getTopicLabel($id),
        'selected' => ($id === $filter[ 'type' ])
    ];
}


include TOPICBANK_BASE_DIR . '/ui/templates/topics.tpl.php';
