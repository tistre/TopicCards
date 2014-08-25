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

$topic = $services->topicmap->newTopic();

$topic->setId($services->topicmap->createId());

$name = $topic->newName();
$name->setType('basename');
$name->setValue(trim($_REQUEST[ 'name' ]));

if (! empty($_REQUEST[ 'type' ]))
{
    $type_ids = $_REQUEST[ 'type' ];
    
    if (! is_array($type_ids))
        $type_ids = [ $type_ids ];
        
    $topic->setTypes($type_ids);
}

$ok = $topic->save();

echo json_encode(array( 'id' => $topic->getId(), 'name' => $name->getValue() ));
