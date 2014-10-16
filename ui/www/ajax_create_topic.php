<?php

require_once dirname(dirname(__DIR__)) . '/include/config.php';

$topic = $topicmap->newTopic();

$topic->setId($topicmap->createId());

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
