<?php

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

$name_like = '%' . $_REQUEST[ 'name' ] . '%';
$type = $_REQUEST[ 'type' ];

$results = $services->topicmap->getTopics([ 'name_like' => $name_like, 'type' => $type ]);

$tpl[ 'results' ] = [ ];

foreach ($results as $id)
{
    $tpl[ 'results' ][ ] = 
    [
        'id' => $id,
        'label' => $services->topicmap->getTopicLabel($id)
    ];
}

include TOPICBANK_BASE_DIR . '/ui/templates/search_topic.tpl.php';
